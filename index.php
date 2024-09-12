<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fId = intval($project['f_id']);
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $tId = $_POST['t_id'];
    $status = $_POST['status'];

    // Automatically set the payment date to the current date
    $date = date('Y-m-d'); // Format: YYYY-MM-DD

    // Check if payment for this project already exists
    $checkPaymentQuery = "SELECT COUNT(*) as count FROM f_payment WHERE project_id = ?";
    $stmt = $conn->prepare($checkPaymentQuery);
    $stmt->bind_param('i', $projectId);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    $paymentExists = $checkResult->fetch_assoc()['count'] > 0;

    if ($paymentExists) {
        // Display alert if payment already exists
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Payment for this project already exists.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'on-same-page.php?id=$projectId';
                });
              </script>";
    } else {
        // Insert payment details into the database
        $paymentQuery = "INSERT INTO f_payment (f_id, project_id, amount, method, 
                        t_id, status, date) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($paymentQuery);
        $stmt->bind_param('iisssss', $fId, $projectId, $amount, $method, 
                          $tId, $status, $date);

        if ($stmt->execute()) {
            // Redirect with success message
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Payment details updated successfully.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'assigned-page-address.php';
                    });
                  </script>";
        } else {
            // Redirect with error message
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating payment details.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'on-same-page.php?id=$projectId';
                    });
                  </script>";
        }
    }
}
?>
