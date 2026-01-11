<?php
$signup_type = isset($_GET['type']) ? $_GET['type'] : 'customer';
$is_vendor_signup = ($signup_type === 'vendor');
$page_title = $is_vendor_signup ? 'Vendor Registration' : 'Sign Up';
$subtitle = $is_vendor_signup ? 'Register as a vendor to sell your products' : 'Create your account';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/auth.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning</span>
        </div>

        <div class="search-container">
            <input type="text" placeholder="Search food...">
            <div class="search-icon">🔍</div>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="orders.php">Orders</a>
            <a href="cart.php" class="cart-link">
                Cart
                <span class="cart-badge">0</span>
            </a>
        </nav>

        <div class="login-dropdown">
            <button class="login-btn" onclick="toggleLoginDropdown()">Login</button>
            <div class="dropdown-content" id="loginDropdown">
                <a href="login.php?type=customer">Customer Login</a>
                <a href="login.php?type=admin">Admin Login</a>
                <a href="login.php?type=vendor">Vendor Login</a>
                <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-box">
            <h1><?php echo $is_vendor_signup ? '🏪 ' . $page_title : $page_title; ?></h1>
            <p class="auth-subtitle"><?php echo $subtitle; ?></p>
            <?php if ($is_vendor_signup): ?>
                <div class="message info" style="margin-bottom: 20px; background-color: #fff3cd; border-color: #ffc107; color: #856404;">
                    ℹ️ Your vendor account will be reviewed by our admin team. You'll receive access within 24-48 hours after approval.
                </div>
            <?php endif; ?>

            <div id="messageContainer"></div>

            <form id="signupForm" onsubmit="handleSignup(event)">
                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($signup_type); ?>">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" placeholder="Enter your name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" placeholder="Enter your email" required>
                        <span class="input-icon">✉️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-wrapper">
                        <input type="tel" name="phone" placeholder="Enter your phone number">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address" placeholder="Enter your address">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Sign Up</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php<?php echo $is_vendor_signup ? '?type=vendor' : ''; ?>">Login</a>
            </div>

            <?php if (!$is_vendor_signup): ?>
                <div class="auth-footer" style="margin-top: 10px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                    Want to sell on our platform? <a href="signup.php?type=vendor" style="color: #ff6b35; font-weight: 600;">Become a Vendor</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        function handleSignup(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');

            // Validate passwords match
            if (password !== confirmPassword) {
                document.getElementById('messageContainer').innerHTML =
                    '<div class="message error">Passwords do not match!</div>';
                return;
            }

            fetch('../php/signup_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('messageContainer');

                    if (data.success) {
                        // Check if user is a vendor and needs approval
                        if (data.user_type === 'vendor' && data.is_approved === 0) {
                            messageContainer.innerHTML = '<div class="message success">Account created successfully! Redirecting to approval page...</div>';
                            setTimeout(() => {
                                window.location.href = 'vendor-pending.php';
                            }, 2000);
                        } else {
                            messageContainer.innerHTML = '<div class="message success">Account created successfully! Redirecting to login...</div>';
                            setTimeout(() => {
                                window.location.href = 'login.php?type=customer';
                            }, 2000);
                        }
                    } else {
                        messageContainer.innerHTML = '<div class="message error">' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('messageContainer').innerHTML =
                        '<div class="message error">An error occurred. Please try again.</div>';
                });
        }
    </script>
</body>

</html>