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

        if (strtolower($this->request->getMethod()) === 'post') {
            $db = \Config\Database::connect('default');
            $ip = $this->request->getIPAddress();

            // 1. Brute Force Protection (Rate Limiting via Session / Time Lock)
            $failCount = (int) $session->get('login_attempts_' . md5($ip));
            $lockUntil = (int) $session->get('login_lockout_' . md5($ip));

            if ($lockUntil > time()) {
                $remainingSec = $lockUntil - time();
                $session->setFlashdata('error', "Terlalu banyak percobaan login yang gagal. Akun dikunci sementara selama {$remainingSec} detik demi keamanan.");
                return redirect()->back();
            }

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
                            ->orWhere('email', $username)
                            ->get()
                            ->getRow();

            if ($userQuery && password_verify($password, $userQuery->password)) {
                if ($userQuery->status !== 'active') {
                    $session->setFlashdata('error', 'Akun Anda dinonaktifkan oleh administrator.');
                    return redirect()->back();
                }

                // Reset failed attempt counters on success
                $session->remove('login_attempts_' . md5($ip));
                $session->remove('login_lockout_' . md5($ip));

                // 2. Prevent Session Fixation: Regenerate Session ID
                $session->regenerate(true);

                // 3. Get Permissions: Prioritize direct user-level custom permissions, fallback to role permissions
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
                $doctorId = $doctorRow ? (int)$doctorRow->id : null;
                $doctorName = $doctorRow ? $doctorRow->name : null;
                $doctorPolyId = $doctorRow ? (int)$doctorRow->polyclinic_id : null;

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
                    'action'     => 'LOGIN',
                    'module'     => 'Auth',
                    'table_name' => 'users',
                    'record_id'  => $userQuery->id,
                    'old_value'  => null,
                    'new_value'  => 'User ' . $userQuery->username . ' login berhasil',
                    'ip_address' => $ip,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

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

        return view('auth/login');
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
