<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Custom Theme files -->
<link href="../css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- Font Awesome untuk ikon mata -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<!-- web font -->
<link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">

<style>
  .password-container {
    position: relative;
    display: flex;
    align-items: center;
  }
  .password-container input {
    flex: 1;
    padding-right: 40px;
  }
  .password-container .toggle-password {
    position: absolute;
    right: 10px;
    cursor: pointer;
    font-size: 18px;
    color: #666;
  }
  .password-container .toggle-password:hover {
    color: #000;
  }
</style>
</head>
<body>
  <!-- main -->
  <div class="main-w3layouts wrapper">
    <h1>Login Beswan</h1>
    <div class="main-agileinfo">
      <div class="agileits-top">
        <form action="pro_login_karyawan.php" method="post">
          <input class="text" type="text" name="username" placeholder="Username" required=""><br>

          <div class="password-container">
            <input class="text" type="password" name="password" id="password" placeholder="Password" required="">
            <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
          </div>
          
          <input type="submit" value="Login">
        </form>
      </div>
    </div>
    <!-- copyright -->
    <div class="colorlibcopy-agile">
      <p>© KSE UINSU 2025</p>
    </div>
  </div>

  <script>
    document.getElementById("togglePassword").addEventListener("click", function() {
      var passwordInput = document.getElementById("password");
      var icon = document.getElementById("togglePassword");
      
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    });
  </script>
</body>
</html>
