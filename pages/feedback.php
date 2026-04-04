<?php
$customer_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $message = $_POST['message'];
    $pdo->prepare("INSERT INTO feedback (customer_id, message) VALUES (?, ?)")->execute([$customer_id, $message]);
    $_SESSION['success'] = "Thank you for your feedback!";
    header("Location: index.php?page=feedback");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Send Feedback</h3>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-3">We'd love to hear from you!</h5>
            <p class="text-muted mb-4">Please share your experience, suggestions, or any issues you faced. Your feedback helps us improve SmartCart.</p>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Your Feedback</label>
                    <textarea name="message" class="form-control bg-light" rows="5" required placeholder="Type your message here..."></textarea>
                </div>
                <button type="submit" name="submit_feedback" class="btn btn-primary-gradient rounded-pill px-4 py-2 fw-bold w-100">Send Feedback</button>
            </form>
        </div>
    </div>
</div>
