<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        if ($session->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        // Konfigurasi Login Akun Google (OAuth 2.0 / Google Identity Services)
        $googleLoginEnabled = in_array(clinic_setting('google_login_enabled', '0'), ['1', 'true', 'on', 1], true);
        $googleClientId     = trim((string) clinic_setting('google_client_id', ''));
        $isGoogleAuthActive = $googleLoginEnabled && $googleClientId !== '';
        $googleLoginMode    = $isGoogleAuthActive ? (string) clinic_setting('google_login_mode', 'both') : 'both';
        if (!in_array($googleLoginMode, ['both', 'staff_google_only', 'google_only'], true)) {
            $googleLoginMode = 'both';
        }

        $postedAuthType = strtolower(trim((string) $this->request->getPost('auth_type')));
        $hideManualFormByDefault = ($isGoogleAuthActive && $googleLoginMode === 'google_only' && $postedAuthType !== 'password');

        if (strtolower($this->request->getMethod()) === 'post') {
            $db = \Config\Database::connect('default');
            $ip = $this->request->getIPAddress();

            // 1. Brute Force Protection (Rate Limiting via Session / Time Lock per IP)
            $failCount = (int) $session->get('login_attempts_' . md5($ip));
            $lockUntil = (int) $session->get('login_lockout_' . md5($ip));

            if ($lockUntil > time()) {
                $remainingSec = $lockUntil - time();
                $session->setFlashdata('error', "Terlalu banyak percobaan login yang gagal. Akun dikunci sementara selama {$remainingSec} detik demi keamanan.");
                return redirect()->back();
            }

            // =================================================================
            // HANDLER A: LOGIN MENGGUNAKAN AKUN GOOGLE (OAUTH 2.0 GIS)
            // =================================================================
            if ($postedAuthType === 'google') {
                if (!$isGoogleAuthActive) {
                    $session->setFlashdata('error', 'Login menggunakan Akun Google saat ini belum diaktifkan.');
                    return redirect()->back();
                }

                $credential = trim((string) $this->request->getPost('google_credential'));
                $googleErr  = '';
                $googlePayload = $this->verifyGoogleIdToken($credential, $googleClientId, $googleErr);

                if (!$googlePayload) {
                    $failCount++;
                    $session->set('login_attempts_' . md5($ip), $failCount);
                    if ($failCount >= 5) {
                        $session->set('login_lockout_' . md5($ip), time() + 60);
                    }
                    $session->setFlashdata('error', $googleErr ?: 'Autentikasi Google gagal.');
                    return redirect()->back();
                }

                $googleEmail = $googlePayload['email'];
                $emailKey    = 'login_lockout_email_' . md5($googleEmail);
                $emailLock   = (int) $session->get($emailKey);
                if ($emailLock > time()) {
                    $remainingSec = $emailLock - time();
                    $session->setFlashdata('error', "Akun {$googleEmail} dikunci sementara selama {$remainingSec} detik demi keamanan.");
                    return redirect()->back();
                }

                $userQuery = $this->findUserByEmail($db, $googleEmail);

                if (!$userQuery) {
                    $failCount++;
                    $session->set('login_attempts_' . md5($ip), $failCount);
                    if ($failCount >= 5) {
                        $session->set('login_lockout_' . md5($ip), time() + 60);
                        $session->set($emailKey, time() + 60);
                    }

                    $db->table('audit_logs')->insert([
                        'user_id'    => null,
                        'action'     => 'LOGIN_FAILED',
                        'module'     => 'Auth',
                        'table_name' => 'users',
                        'record_id'  => 0,
                        'old_value'  => null,
                        'new_value'  => 'Login Google ditolak — email tidak terdaftar: ' . $googleEmail,
                        'ip_address' => $ip,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $session->setFlashdata('error', "Email Google <strong>{$googleEmail}</strong> belum terdaftar pada sistem. Hubungi Administrator untuk mendaftarkan email Anda.");
                    return redirect()->back();
                }

                if ($userQuery->status !== 'active') {
                    $session->setFlashdata('error', 'Akun Anda dinonaktifkan oleh administrator.');
                    return redirect()->back();
                }

                // Reset failed attempts on success
                $session->remove('login_attempts_' . md5($ip));
                $session->remove('login_lockout_' . md5($ip));
                $session->remove($emailKey);

                $this->completeLogin(
                    $db,
                    $session,
                    $userQuery,
                    $ip,
                    'LOGIN_GOOGLE',
                    'User ' . $userQuery->username . ' login berhasil via Akun Google (' . $googleEmail . ')'
                );

                return redirect()->to(base_url('dashboard'));
            }

            // =================================================================
            // HANDLER B: LOGIN MANUAL (USERNAME / EMAIL & PASSWORD)
            // =================================================================
            $username = trim(strip_tags((string) $this->request->getPost('username')));
            $password = (string) $this->request->getPost('password');

            if (empty($username) || empty($password)) {
                $session->setFlashdata('error', 'Username dan Password wajib diisi.');
                return redirect()->back();
            }

            $userQuery = $db->table('users')
                            ->select('users.*, roles.name as role_name')
                            ->join('roles', 'roles.id = users.role_id')
                            ->where('username', $username)
                            ->orWhere('LOWER(TRIM(email))', strtolower($username))
                            ->get()
                            ->getRow();

            if ($userQuery && password_verify($password, $userQuery->password)) {
                if ($userQuery->status !== 'active') {
                    $session->setFlashdata('error', 'Akun Anda dinonaktifkan oleh administrator.');
                    return redirect()->back();
                }

                // Pembatasan Mode Login Google (Wajib Google bagi staf non-admin)
                if ($isGoogleAuthActive && in_array($googleLoginMode, ['staff_google_only', 'google_only'], true)) {
                    $isAdminRole = in_array(strtolower((string) $userQuery->role_name), ['super admin', 'admin', 'administrator', 'it'], true);
                    if (!$isAdminRole) {
                        $session->setFlashdata('error', 'Kebijakan sistem mewajibkan staf login menggunakan tombol <strong>Masuk dengan Google</strong>.');
                        return redirect()->back();
                    }
                }

                // Reset failed attempt counters on success
                $session->remove('login_attempts_' . md5($ip));
                $session->remove('login_lockout_' . md5($ip));

                $this->completeLogin(
                    $db,
                    $session,
                    $userQuery,
                    $ip,
                    'LOGIN',
                    'User ' . $userQuery->username . ' login berhasil'
                );

                return redirect()->to(base_url('dashboard'));
            } else {
                // Increment failed attempts
                $failCount++;
                $session->set('login_attempts_' . md5($ip), $failCount);

                // Audit Log: Failed Login Attempt
                $db->table('audit_logs')->insert([
                    'user_id'    => $userQuery ? $userQuery->id : null,
                    'action'     => 'LOGIN_FAILED',
                    'module'     => 'Auth',
                    'table_name' => 'users',
                    'record_id'  => $userQuery ? $userQuery->id : 0,
                    'old_value'  => null,
                    'new_value'  => 'Percobaan login gagal untuk username: ' . $username,
                    'ip_address' => $ip,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if ($failCount >= 5) {
                    $session->set('login_lockout_' . md5($ip), time() + 60);
                    $session->setFlashdata('error', 'Terlalu banyak percobaan gagal (5x). Akses login dikunci sementara selama 60 detik.');
                } else {
                    $sisaPercobaan = 5 - $failCount;
                    $session->setFlashdata('error', "Username atau Password salah. (Sisa percobaan: {$sisaPercobaan}x sebelum dikunci)");
                }

                return redirect()->back();
            }
        }

        return view('auth/login', [
            'googleLoginEnabled'      => $googleLoginEnabled,
            'googleClientId'          => $googleClientId,
            'isGoogleAuthActive'      => $isGoogleAuthActive,
            'googleLoginMode'         => $googleLoginMode,
            'hideManualFormByDefault' => $hideManualFormByDefault,
        ]);
    }

    /**
     * Pencarian akun pengguna berdasarkan email (case-insensitive & trimmed)
     */
    private function findUserByEmail($db, string $email)
    {
        $email = trim($email);
        if ($email === '') {
            return null;
        }

        return $db->table('users')
                  ->select('users.*, roles.name as role_name')
                  ->join('roles', 'roles.id = users.role_id')
                  ->where('LOWER(TRIM(users.email))', strtolower($email))
                  ->limit(1)
                  ->get()
                  ->getRow();
    }

    /**
     * Verifikasi Server-Side JWT ID Token Google melalui endpoint resmi Google OAuth2
     */
    private function verifyGoogleIdToken(string $idToken, string $expectedClientId, string &$errorMessage = ''): ?array
    {
        $idToken          = trim($idToken);
        $expectedClientId = trim($expectedClientId);

        if ($idToken === '' || $expectedClientId === '') {
            $errorMessage = 'Token atau Google Client ID belum dikonfigurasi.';
            return null;
        }

        $verifyUrl   = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
        $response    = false;
        $httpCode    = 0;
        $host        = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $isLocalhost = ($host === 'localhost' || strpos($host, '127.0.0.1') === 0 || strpos($host, 'localhost:') === 0);

        if (function_exists('curl_init')) {
            $ch = curl_init($verifyUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $response  = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Fallback otomatis khusus localhost XAMPP Windows bila CA bundle belum terpasang
            if (($response === false || $curlErrno === 60 || $curlErrno === 77) && $isLocalhost) {
                $ch = curl_init($verifyUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }
        }

        if ($response === false || $httpCode !== 200) {
            $errorMessage = 'Gagal memverifikasi kredensial dengan server Google.';
            return null;
        }

        $payload = json_decode((string) $response, true);
        if (!is_array($payload) || isset($payload['error'])) {
            $errorMessage = 'Token Google tidak valid atau telah kedaluwarsa.';
            return null;
        }

        if (($payload['aud'] ?? '') !== $expectedClientId) {
            $errorMessage = 'Google Client ID tidak cocok dengan aplikasi ini.';
            return null;
        }

        if (!in_array($payload['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) {
            $errorMessage = 'Penerbit token (issuer) tidak sah.';
            return null;
        }

        if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
            $errorMessage = 'Sesi login Google telah kedaluwarsa. Silakan coba lagi.';
            return null;
        }

        $emailVerified = $payload['email_verified'] ?? false;
        if (empty($payload['email']) || ($emailVerified !== true && $emailVerified !== 'true' && $emailVerified !== '1')) {
            $errorMessage = 'Alamat email Google belum terverifikasi.';
            return null;
        }

        return [
            'sub'     => (string) ($payload['sub'] ?? ''),
            'email'   => strtolower(trim((string) $payload['email'])),
            'name'    => trim((string) ($payload['name'] ?? '')),
            'picture' => trim((string) ($payload['picture'] ?? ''))
        ];
    }

    /**
     * Penyelesaian sesi login terpadu (mencegah Session Fixation & memuat RBAC)
     */
    private function completeLogin($db, $session, $userQuery, string $ip, string $auditAction = 'LOGIN', string $auditDesc = '')
    {
        // Prevent Session Fixation: Regenerate Session ID
        $session->regenerate(true);

        // Get Permissions: Prioritize direct user-level custom permissions, fallback to role permissions
        $permissions = [];
        try {
            $userPermsQuery = $db->table('user_permissions')
                                 ->select('permissions.name')
                                 ->join('permissions', 'permissions.id = user_permissions.permission_id')
                                 ->where('user_permissions.user_id', $userQuery->id)
                                 ->get()
                                 ->getResult();

            if (!empty($userPermsQuery)) {
                foreach ($userPermsQuery as $perm) {
                    $permissions[] = $perm->name;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'user_permissions query error: ' . $e->getMessage());
        }

        if (empty($permissions)) {
            try {
                $rolePermsQuery = $db->table('role_permissions')
                                      ->select('permissions.name')
                                      ->join('permissions', 'permissions.id = role_permissions.permission_id')
                                      ->where('role_id', $userQuery->role_id)
                                      ->get()
                                      ->getResult();
                if (!empty($rolePermsQuery)) {
                    foreach ($rolePermsQuery as $perm) {
                        $permissions[] = $perm->name;
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'role_permissions query error: ' . $e->getMessage());
            }
        }

        // Check if user is linked to a doctor profile
        $doctorRow = $db->table('doctors')
                        ->where('user_id', $userQuery->id)
                        ->orWhere('id', ($userQuery->role_name === 'Dokter' ? 1 : 0))
                        ->where('status', 'active')
                        ->get()
                        ->getRow();
        $doctorId     = $doctorRow ? (int) $doctorRow->id : null;
        $doctorName   = $doctorRow ? $doctorRow->name : null;
        $doctorPolyId = $doctorRow ? (int) $doctorRow->polyclinic_id : null;

        // Set session
        $session->set([
            'user_id'        => $userQuery->id,
            'username'       => $userQuery->username,
            'email'          => $userQuery->email,
            'role_id'        => $userQuery->role_id,
            'role_name'      => $userQuery->role_name,
            'doctor_id'      => $doctorId,
            'doctor_name'    => $doctorName,
            'doctor_poly_id' => $doctorPolyId,
            'permissions'    => $permissions,
            'logged_in'      => true
        ]);

        // Audit Log: Success Login
        $db->table('audit_logs')->insert([
            'user_id'    => $userQuery->id,
            'action'     => $auditAction,
            'module'     => 'Auth',
            'table_name' => 'users',
            'record_id'  => $userQuery->id,
            'old_value'  => null,
            'new_value'  => $auditDesc !== '' ? $auditDesc : ('User ' . $userQuery->username . ' login berhasil'),
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function logout()
    {
        $session = session();
        if ($session->get('logged_in')) {
            $db = \Config\Database::connect('default');
            $db->table('audit_logs')->insert([
                'user_id'    => $session->get('user_id'),
                'action'     => 'LOGOUT',
                'module'     => 'Auth',
                'table_name' => 'users',
                'record_id'  => $session->get('user_id'),
                'old_value'  => null,
                'new_value'  => 'User logged out',
                'ip_address' => $this->request->getIPAddress()
            ]);
        }

        $session->destroy();
        return redirect()->to(base_url('login'));
    }
}
