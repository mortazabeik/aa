<?php
require_once 'config.php';

// Check authentication
$isLoggedIn = isAuthenticated();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $db = getDB();
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_username'] = $username;
            header('Location: admin.php');
            exit();
        } else {
            $loginError = 'نام کاربری یا رمز عبور اشتباه است';
        }
    }
    
    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        header('Location: admin.php');
        exit();
    }
    
    if ($isLoggedIn) {
        $db = getDB();
        
        if ($action === 'update_martyr') {
            $id = $_POST['id'];
            $sql = "UPDATE martyrs SET 
                code_isar = ?, national_id = ?, veteran_status = ?, first_name = ?, last_name = ?, father_name = ?,
                gender = ?, nationality = ?, religion = ?, birth_date = ?, birth_year = ?, birth_month = ?, birth_day = ?,
                martyrdom_date = ?, martyrdom_year = ?, martyrdom_month = ?, martyrdom_day = ?, age = ?,
                birth_place = ?, file_location = ?, burial_place = ?, education = ?, occupation = ?, marital_status = ?,
                serving_unit = ?, membership_type = ?, event_stream = ?, operation_zone = ?, enemy = ?, military_operation = ?
                WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $_POST['code_isar'], $_POST['national_id'], $_POST['veteran_status'], $_POST['first_name'], $_POST['last_name'], $_POST['father_name'],
                $_POST['gender'], $_POST['nationality'], $_POST['religion'], $_POST['birth_date'], $_POST['birth_year'], $_POST['birth_month'], $_POST['birth_day'],
                $_POST['martyrdom_date'], $_POST['martyrdom_year'], $_POST['martyrdom_month'], $_POST['martyrdom_day'], $_POST['age'] ?: null,
                $_POST['birth_place'], $_POST['file_location'], $_POST['burial_place'], $_POST['education'], $_POST['occupation'], $_POST['marital_status'],
                $_POST['serving_unit'], $_POST['membership_type'], $_POST['event_stream'], $_POST['operation_zone'], $_POST['enemy'], $_POST['military_operation'],
                $id
            ]);
            
            $successMessage = 'اطلاعات شهید با موفقیت بروزرسانی شد';
        }
        
        if ($action === 'delete_martyr') {
            $id = $_POST['id'];
            $stmt = $db->prepare("DELETE FROM martyrs WHERE id = ?");
            $stmt->execute([$id]);
            $successMessage = 'شهید با موفقیت حذف شد';
        }
        
        if ($action === 'upload_image') {
            $martyrId = $_POST['martyr_id'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/martyrs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = $martyrId . '_' . time() . '.' . $extension;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imageUrl = 'uploads/martyrs/' . $filename;
                    $stmt = $db->prepare("UPDATE martyrs SET profile_image = ? WHERE id = ?");
                    $stmt->execute([$imageUrl, $martyrId]);
                    $successMessage = 'تصویر با موفقیت آپلود شد';
                }
            }
        }
        
        if ($action === 'approve_submission') {
            $id = $_POST['id'];
            $stmt = $db->prepare("UPDATE submissions SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            $successMessage = 'اثر ارسالی تایید شد';
        }
        
        if ($action === 'reject_submission') {
            $id = $_POST['id'];
            $stmt = $db->prepare("UPDATE submissions SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$id]);
            $successMessage = 'اثر ارسالی رد شد';
        }
        
        if ($action === 'delete_submission') {
            $id = $_POST['id'];
            $stmt = $db->prepare("DELETE FROM submissions WHERE id = ?");
            $stmt->execute([$id]);
            $successMessage = 'اثر ارسالی حذف شد';
        }
        
        if ($action === 'add_slider') {
            if (isset($_FILES['slider']) && $_FILES['slider']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/sliders/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($_FILES['slider']['name'], PATHINFO_EXTENSION);
                $filename = 'slider_' . time() . '.' . $extension;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['slider']['tmp_name'], $targetPath)) {
                    $stmt = $db->query("SELECT MAX(sort_order) as max_order FROM sliders");
                    $result = $stmt->fetch();
                    $nextOrder = ($result['max_order'] ?? -1) + 1;
                    
                    $stmt = $db->prepare("INSERT INTO sliders (url, sort_order) VALUES (?, ?)");
                    $stmt->execute(['uploads/sliders/' . $filename, $nextOrder]);
                    $successMessage = 'اسلایدر با موفقیت اضافه شد';
                }
            }
        }
        
        if ($action === 'delete_slider') {
            $id = $_POST['id'];
            $stmt = $db->prepare("DELETE FROM sliders WHERE id = ?");
            $stmt->execute([$id]);
            $successMessage = 'اسلایدر حذف شد';
        }
    }
}

// Get data for logged in users
if ($isLoggedIn) {
    $db = getDB();
    
    $search = $_GET['search'] ?? '';
    $tab = $_GET['tab'] ?? 'martyrs';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 50;
    $offset = ($page - 1) * $limit;
    
    // Get martyrs
    $martyrsWhere = "";
    $martyrsParams = [];
    if ($search) {
        $martyrsWhere = "WHERE first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ? OR code_isar LIKE ?";
        $searchTerm = '%' . $search . '%';
        $martyrsParams = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    }
    
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM martyrs $martyrsWhere");
    $countStmt->execute($martyrsParams);
    $totalMartyrs = $countStmt->fetch()['total'];
    $totalPages = ceil($totalMartyrs / $limit);
    
    $martyrsParams[] = $limit;
    $martyrsParams[] = $offset;
    $stmt = $db->prepare("SELECT * FROM martyrs $martyrsWhere ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmt->execute($martyrsParams);
    $martyrs = $stmt->fetchAll();
    
    // Get submissions
    $stmt = $db->query("SELECT s.*, m.first_name, m.last_name FROM submissions s LEFT JOIN martyrs m ON s.martyr_id = m.id ORDER BY s.submitted_at DESC");
    $submissions = $stmt->fetchAll();
    
    // Get sliders
    $stmt = $db->query("SELECT * FROM sliders ORDER BY sort_order ASC");
    $sliders = $stmt->fetchAll();
    
    // Get single martyr for editing
    $editMartyr = null;
    if (isset($_GET['edit'])) {
        $stmt = $db->prepare("SELECT * FROM martyrs WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $editMartyr = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت - گلزار تربت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Vazirmatn', sans-serif; }
        .glass { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass { 
            background: rgba(0, 0, 0, 0.4); 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-100 via-blue-50 to-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <?php if (!$isLoggedIn): ?>
        <!-- Login Form -->
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="glass w-full max-w-md p-8 rounded-3xl shadow-xl">
                <div class="text-center mb-8">
                    <div class="inline-flex p-4 bg-blue-100 text-blue-600 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">ورود به پنل مدیریت</h1>
                    <p class="text-gray-500 mt-2">اطلاعات ورود را وارد کنید</p>
                </div>
                
                <?php if (isset($loginError)): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-xl text-sm"><?php echo $loginError; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <input type="text" name="username" placeholder="نام کاربری..." required
                            class="w-full p-4 border border-gray-200 bg-white/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" dir="ltr">
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="رمز عبور..." required
                            class="w-full p-4 border border-gray-200 bg-white/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" dir="ltr">
                    </div>
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors">
                        ورود
                    </button>
                </form>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Admin Panel -->
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-800">پنل مدیریت</h1>
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="flex items-center gap-2 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        خروج
                    </button>
                </form>
            </div>
            
            <?php if (isset($successMessage)): ?>
            <div class="p-4 bg-green-100 text-green-700 rounded-xl"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <!-- Tabs -->
            <div class="glass p-2 rounded-2xl">
                <div class="flex overflow-x-auto gap-2">
                    <a href="?tab=martyrs" class="flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap <?php echo $tab === 'martyrs' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-white/50'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        مدیریت شهدا
                    </a>
                    <a href="?tab=submissions" class="flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap <?php echo $tab === 'submissions' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-white/50'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        بررسی آثار ارسالی
                    </a>
                    <a href="?tab=gallery" class="flex items-center gap-2 px-6 py-3 rounded-xl font-medium transition-all whitespace-nowrap <?php echo $tab === 'gallery' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-white/50'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        اسلایدر و گالری
                    </a>
                </div>
            </div>
            
            <?php if ($tab === 'martyrs'): ?>
            <!-- Martyrs Tab -->
            <div class="space-y-6">
                <!-- Search -->
                <div class="glass p-4 rounded-2xl">
                    <form method="GET" class="flex gap-4">
                        <input type="hidden" name="tab" value="martyrs">
                        <div class="relative flex-1">
                            <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="جستجو با نام، کد ملی یا کد ایثار..."
                                class="w-full pr-12 pl-4 py-3 border border-gray-200 bg-white/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-colors">
                            جستجو
                        </button>
                    </form>
                </div>
                
                <?php if ($editMartyr): ?>
                <!-- Edit Martyr Form -->
                <div class="glass p-6 rounded-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">ویرایش مشخصات شهید</h2>
                        <a href="?tab=martyrs&search=<?php echo urlencode($search); ?>" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                    
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update_martyr">
                        <input type="hidden" name="id" value="<?php echo $editMartyr['id']; ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نام</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($editMartyr['first_name']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نام خانوادگی</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($editMartyr['last_name']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نام پدر</label>
                                <input type="text" name="father_name" value="<?php echo htmlspecialchars($editMartyr['father_name']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">کد ملی</label>
                                <input type="text" name="national_id" value="<?php echo htmlspecialchars($editMartyr['national_id']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">کد ایثار</label>
                                <input type="text" name="code_isar" value="<?php echo htmlspecialchars($editMartyr['code_isar']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت ایثارگری</label>
                                <input type="text" name="veteran_status" value="<?php echo htmlspecialchars($editMartyr['veteran_status']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">جنسیت</label>
                                <input type="text" name="gender" value="<?php echo htmlspecialchars($editMartyr['gender']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملیت</label>
                                <input type="text" name="nationality" value="<?php echo htmlspecialchars($editMartyr['nationality']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مذهب</label>
                                <input type="text" name="religion" value="<?php echo htmlspecialchars($editMartyr['religion']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ تولد</label>
                                <input type="text" name="birth_date" value="<?php echo htmlspecialchars($editMartyr['birth_date']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">سال تولد</label>
                                <input type="text" name="birth_year" value="<?php echo htmlspecialchars($editMartyr['birth_year']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ماه تولد</label>
                                <input type="text" name="birth_month" value="<?php echo htmlspecialchars($editMartyr['birth_month']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">روز تولد</label>
                                <input type="text" name="birth_day" value="<?php echo htmlspecialchars($editMartyr['birth_day']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ شهادت</label>
                                <input type="text" name="martyrdom_date" value="<?php echo htmlspecialchars($editMartyr['martyrdom_date']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">سال شهادت</label>
                                <input type="text" name="martyrdom_year" value="<?php echo htmlspecialchars($editMartyr['martyrdom_year']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ماه شهادت</label>
                                <input type="text" name="martyrdom_month" value="<?php echo htmlspecialchars($editMartyr['martyrdom_month']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">روز شهادت</label>
                                <input type="text" name="martyrdom_day" value="<?php echo htmlspecialchars($editMartyr['martyrdom_day']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">سن شهادت</label>
                                <input type="number" name="age" value="<?php echo htmlspecialchars($editMartyr['age']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">محل تولد</label>
                                <input type="text" name="birth_place" value="<?php echo htmlspecialchars($editMartyr['birth_place']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">محل پرونده</label>
                                <input type="text" name="file_location" value="<?php echo htmlspecialchars($editMartyr['file_location']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">محل گلزار</label>
                                <input type="text" name="burial_place" value="<?php echo htmlspecialchars($editMartyr['burial_place']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تحصیلات</label>
                                <input type="text" name="education" value="<?php echo htmlspecialchars($editMartyr['education']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">شغل</label>
                                <input type="text" name="occupation" value="<?php echo htmlspecialchars($editMartyr['occupation']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت تاهل</label>
                                <input type="text" name="marital_status" value="<?php echo htmlspecialchars($editMartyr['marital_status']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">یگان خدمت</label>
                                <input type="text" name="serving_unit" value="<?php echo htmlspecialchars($editMartyr['serving_unit']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نوع عضویت</label>
                                <input type="text" name="membership_type" value="<?php echo htmlspecialchars($editMartyr['membership_type']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">جریان حادثه</label>
                                <input type="text" name="event_stream" value="<?php echo htmlspecialchars($editMartyr['event_stream']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">منطقه عملیاتی</label>
                                <input type="text" name="operation_zone" value="<?php echo htmlspecialchars($editMartyr['operation_zone']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دشمن</label>
                                <input type="text" name="enemy" value="<?php echo htmlspecialchars($editMartyr['enemy']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">عملیات نظامی</label>
                                <input type="text" name="military_operation" value="<?php echo htmlspecialchars($editMartyr['military_operation']); ?>"
                                    class="w-full p-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <a href="?tab=martyrs&search=<?php echo urlencode($search); ?>" class="px-6 py-2.5 rounded-xl font-medium text-gray-600 hover:bg-gray-200 transition-colors">
                                انصراف
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Martyrs Table -->
                <div class="glass rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-sm">
                            <thead class="bg-white/50 text-gray-700">
                                <tr>
                                    <th class="p-4 font-bold">تصویر</th>
                                    <th class="p-4 font-bold">نام و نام خانوادگی</th>
                                    <th class="p-4 font-bold">کد ملی</th>
                                    <th class="p-4 font-bold">کد ایثار</th>
                                    <th class="p-4 font-bold">عملیات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/30">
                                <?php foreach ($martyrs as $martyr): ?>
                                <tr class="hover:bg-white/20 transition-colors">
                                    <td class="p-4">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                            <?php if ($martyr['profile_image']): ?>
                                            <img src="../<?php echo htmlspecialchars($martyr['profile_image']); ?>" alt="<?php echo htmlspecialchars($martyr['first_name']); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="p-4 font-medium text-gray-800"><?php echo htmlspecialchars($martyr['first_name'] . ' ' . $martyr['last_name']); ?></td>
                                    <td class="p-4 font-mono text-gray-600"><?php echo htmlspecialchars($martyr['national_id']); ?></td>
                                    <td class="p-4 font-mono text-gray-600"><?php echo htmlspecialchars($martyr['code_isar']); ?></td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <a href="?tab=martyrs&edit=<?php echo $martyr['id']; ?>&search=<?php echo urlencode($search); ?>" 
                                                class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="ویرایش مشخصات">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" enctype="multipart/form-data" class="inline">
                                                <input type="hidden" name="action" value="upload_image">
                                                <input type="hidden" name="martyr_id" value="<?php echo $martyr['id']; ?>">
                                                <label class="cursor-pointer p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors inline-block" title="آپلود تصویر">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    <input type="file" name="image" accept="image/*" class="hidden" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                            <form method="POST" class="inline" onsubmit="return confirm('آیا از حذف این شهید اطمینان دارید؟')">
                                                <input type="hidden" name="action" value="delete_martyr">
                                                <input type="hidden" name="id" value="<?php echo $martyr['id']; ?>">
                                                <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="حذف">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="p-4 border-t border-gray-200/30 flex justify-center gap-2">
                        <?php if ($page > 1): ?>
                        <a href="?tab=martyrs&search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-white/50 hover:bg-white rounded-lg transition-colors">قبلی</a>
                        <?php endif; ?>
                        
                        <span class="px-4 py-2 bg-blue-500 text-white rounded-lg">صفحه <?php echo $page; ?> از <?php echo $totalPages; ?></span>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?tab=martyrs&search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-white/50 hover:bg-white rounded-lg transition-colors">بعدی</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($tab === 'submissions'): ?>
            <!-- Submissions Tab -->
            <div class="space-y-6">
                <?php if (empty($submissions)): ?>
                <div class="glass p-12 rounded-2xl text-center">
                    <div class="inline-flex p-4 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">اثری برای بررسی وجود ندارد</h3>
                    <p class="text-gray-500">آثار ارسالی کاربران در اینجا نمایش داده می‌شود</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($submissions as $sub): ?>
                    <div class="glass rounded-2xl overflow-hidden">
                        <div class="aspect-video bg-gray-100">
                            <img src="../<?php echo htmlspecialchars($sub['file_url']); ?>" alt="Submission" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?php 
                                    echo $sub['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                        ($sub['status'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); 
                                ?>">
                                    <?php echo $sub['status'] === 'pending' ? 'در انتظار' : ($sub['status'] === 'approved' ? 'تایید شده' : 'رد شده'); ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500"><?php echo $sub['submitted_at']; ?></p>
                            <div class="flex gap-2">
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="action" value="approve_submission">
                                    <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                    <button type="submit" class="w-full py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">تایید</button>
                                </form>
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="action" value="reject_submission">
                                    <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                    <button type="submit" class="w-full py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">رد</button>
                                </form>
                                <form method="POST" onsubmit="return confirm('آیا از حذف اطمینان دارید؟')">
                                    <input type="hidden" name="action" value="delete_submission">
                                    <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                    <button type="submit" class="p-2 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($tab === 'gallery'): ?>
            <!-- Gallery/Sliders Tab -->
            <div class="space-y-6">
                <!-- Upload New Slider -->
                <div class="glass p-6 rounded-2xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">افزودن اسلایدر جدید</h3>
                    <form method="POST" enctype="multipart/form-data" class="flex gap-4 items-end">
                        <input type="hidden" name="action" value="add_slider">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">تصویر</label>
                            <input type="file" name="slider" accept="image/*" required
                                class="w-full p-2.5 border border-gray-200 rounded-xl bg-white/50">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-medium hover:bg-blue-600 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            افزودن
                        </button>
                    </form>
                </div>
                
                <!-- Sliders List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($sliders as $index => $slider): ?>
                    <div class="glass rounded-2xl overflow-hidden">
                        <div class="aspect-video bg-gray-100">
                            <img src="../<?php echo htmlspecialchars($slider['url']); ?>" alt="Slider" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4 flex justify-between items-center">
                            <span class="text-sm text-gray-600">ترتیب: <?php echo $slider['sort_order'] + 1; ?></span>
                            <form method="POST" onsubmit="return confirm('آیا از حذف این اسلایدر اطمینان دارید؟')">
                                <input type="hidden" name="action" value="delete_slider">
                                <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
