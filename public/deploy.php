<?php
/**
 * =========================================================================
 * GITHUB WEBHOOK AUTO-DEPLOY SCRIPT - SAWAMAWA MEDICAL CENTER
 * =========================================================================
 * Endpoint   : https://appsdemo-albiandra-project.my.id/deploy.php
 *              (atau https://sawamawamedicalcenter.id/deploy.php)
 * Path Server: /home/n1579664/public_html/appsdemo-albiandra-project.my.id
 * Repositori : https://github.com/pahriyadi/gitria_farma.git
 * Fungsi     : Menarik update otomatis (git pull origin main) & migrasi DB
 * =========================================================================
 */

date_default_timezone_set('Asia/Makassar');
header('Content-Type: application/json; charset=utf-8');

// Secret Token untuk validasi keamanan Webhook GitHub
$webhook_secret = 'Sawamawa2026_AutoDeploy_77!';

// 1. Ambil raw payload dan header signature dari GitHub
$raw_payload   = file_get_contents('php://input');
$signature_256 = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256']) ? $_SERVER['HTTP_X_HUB_SIGNATURE_256'] : '';
$github_event  = isset($_SERVER['HTTP_X_GITHUB_EVENT']) ? $_SERVER['HTTP_X_GITHUB_EVENT'] : '';

// Mendukung pemicu manual lewat browser dengan ?key=Sawamawa2026_AutoDeploy_77!
$manual_key = isset($_GET['key']) ? $_GET['key'] : '';
$is_valid_manual = ($manual_key !== '' && hash_equals($webhook_secret, $manual_key));

// 2. Validasi HMAC SHA-256 dari GitHub Webhook
$is_valid_webhook = false;
if (!empty($signature_256) && !empty($raw_payload)) {
    $expected_signature = 'sha256=' . hash_hmac('sha256', $raw_payload, $webhook_secret);
    $is_valid_webhook = hash_equals($expected_signature, $signature_256);
}

if (!$is_valid_webhook && !$is_valid_manual) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Akses ditolak: Signature GitHub Webhook tidak valid.'
    ], JSON_PRETTY_PRINT);
    exit;
}

// 3. Jika event dari GitHub adalah 'ping' (saat pertama kali pasang webhook)
if ($github_event === 'ping') {
    http_response_code(200);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Pong! Webhook Sawamawa Medical Center berhasil terhubung.'
    ], JSON_PRETTY_PRINT);
    exit;
}

// 4. Pastikan hanya memproses jika push dilakukan ke branch 'main'
if ($is_valid_webhook && $github_event === 'push') {
    $payload_data = json_decode($raw_payload, true);
    $ref = isset($payload_data['ref']) ? $payload_data['ref'] : '';
    if ($ref !== '' && $ref !== 'refs/heads/main') {
        http_response_code(200);
        echo json_encode([
            'status'  => 'ignored',
            'message' => "Push ke branch '{$ref}' diabaikan (hanya memproses refs/heads/main)."
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

// Root repository (karena deploy.php berada di public/)
$repo_dir  = dirname(__DIR__);
$log_dir   = $repo_dir . '/writable/logs';
$flag_file = $repo_dir . '/writable/cache/.deploy_trigger';
$log_file  = $log_dir . '/deploy.log';

chdir($repo_dir);

// 5. Eksekusi perintah git & spark menggunakan proc_open
function run_command_in_dir($cmd, $working_dir) {
    $disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
    $full_cmd = $cmd . ' 2>&1';

    if (function_exists('proc_open') && !in_array('proc_open', $disabled, true)) {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $process = @proc_open($cmd, $descriptors, $pipes, $working_dir);
        if (is_resource($process)) {
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $code = proc_close($process);
            return ['output' => trim($stdout . "\n" . $stderr), 'code' => $code, 'method' => 'proc_open'];
        }
    }

    if (function_exists('exec') && !in_array('exec', $disabled, true)) {
        $output = [];
        $return_var = 0;
        @exec($full_cmd, $output, $return_var);
        return ['output' => implode("\n", $output), 'code' => $return_var, 'method' => 'exec'];
    }

    if (function_exists('shell_exec') && !in_array('shell_exec', $disabled, true)) {
        $out = @shell_exec($full_cmd);
        return ['output' => trim((string)$out), 'code' => 0, 'method' => 'shell_exec'];
    }

    return ['output' => '', 'code' => -1, 'method' => 'cron_queue'];
}

$git_bin = 'git';
if (file_exists('/usr/local/cpanel/3rdparty/bin/git')) {
    $git_bin = '/usr/local/cpanel/3rdparty/bin/git';
} elseif (file_exists('/usr/bin/git')) {
    $git_bin = '/usr/bin/git';
}

$php_bin = 'php';
if (file_exists('/usr/local/bin/php')) {
    $php_bin = '/usr/local/bin/php';
}

// Tarik update dari GitHub & jalankan migration/seeder jika ada
$cmd = "{$git_bin} fetch origin main && {$git_bin} reset --hard origin/main && {$git_bin} pull origin main";
$pull_result = run_command_in_dir($cmd, $repo_dir);

// Eksekusi seeder / spark migrate secara aman (opsional jika spark tersedia)
$spark_output = '';
if ($pull_result['code'] === 0 && file_exists($repo_dir . '/spark')) {
    $spark_cmd = "{$php_bin} spark db:seed VoidSystemSeeder";
    $spark_run = run_command_in_dir($spark_cmd, $repo_dir);
    $spark_output = "\n--- Spark Seeder Output ---\n" . $spark_run['output'];
}

if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0775, true);
}
if (is_dir($log_dir) && is_writable($log_dir)) {
    $log_entry = sprintf(
        "[%s WITA] Event: %s | Method: %s | Code: %d\nOutput:\n%s%s\n%s\n",
        date('Y-m-d H:i:s'),
        ($is_valid_manual ? 'manual_trigger' : ($github_event ?: 'webhook')),
        $pull_result['method'],
        $pull_result['code'],
        $pull_result['output'],
        $spark_output,
        str_repeat('-', 60)
    );
    @file_put_contents($log_file, $log_entry, FILE_APPEND);
}

http_response_code($pull_result['code'] === 0 ? 200 : 500);
echo json_encode([
    'status'       => ($pull_result['code'] === 0 ? 'success' : 'error'),
    'timestamp'    => date('Y-m-d H:i:s') . ' WITA',
    'method'       => $pull_result['method'],
    'git_bin'      => $git_bin,
    'output'       => $pull_result['output'] . $spark_output
], JSON_PRETTY_PRINT);
