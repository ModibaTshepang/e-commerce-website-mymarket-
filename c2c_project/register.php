<?php
include "config/database.php";
$message ="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = $_POST["confirm_password"];
    $role_id = $_POST["role_id"];
    $admin_confirmation = $_POST["admin_confirmation"] ??"";

    $check_email= "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($role_id=="3"){
        $correct_admin_code="admin36";
        if ($admin_confirmation !== $correct_admin_code){
            $message="Invalid admin authentication code!";
        }
    }


    if (
        empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)
    ) {
        $message = "Please fill in all fields.";
    
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } 

        if (mysqli_num_rows($result) > 0){
            $message="email already exists!";
            } elseif($role_id==3 && $admin_confirmation !== "admin36"){
                $message="Invalid admin authentication code!";

        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (first_name, last_name, email, password, role_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $email, $hashed_password, $role_id);

            if (mysqli_stmt_execute($stmt)) {
                $message = "registration successful";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }

    }
  


?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <title>Registration page</title>

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body{
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #5f5fff, #6c63ff);
            overflow: hidden;
        }

        .container{
            width: 1100px;
            height: 650px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
            background: white;
        }

        .left-panel{
            width: 50%;
            background: linear-gradient(180deg, #3525ff, #2417d8);
            position: relative;
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .left-panel::before{
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            bottom: -200px;
            left: -100px;
        }

        .left-panel::after{
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
            top: -150px;
            right: -150px;
        }

        .home-link{
            font-size: 28px;
            z-index: 1;
        }

        .left-content{
            z-index: 1;
        }

        .left-content h1{
            font-size: 70px;
            margin-bottom: 30px;
        }

        .left-content p{
            font-size: 28px;
            margin-bottom: 30px;
        }

        .login-btn{
            display: inline-block;
            padding: 18px 70px;
            border: 2px solid white;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-size: 28px;
            transition: 0.3s;
        }

        .login-btn:hover{
            background: white;
            color: #3525ff;
        }

        .right-panel{
            width: 50%;
            background: #f7f7f7;
            padding: 50px 70px;
            position: relative;
        }


        .form-container{
            margin-top: 20px;
        }

        .form-container h2{
            text-align: center;
            color: #4c46ff;
            font-size: 40px;
            margin-bottom: 30px;
        }

        .message{
            background: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 18px;
        }

        .form-group{
            margin-bottom: 5px;
        }

        .form-group label{
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .form-group input,
        .form-group select{
            width: 100%;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 15px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus{
            border-color: #4c46ff;
        }

        .signup-btn{
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(90deg, #4c46ff, #5f5fff);
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .signup-btn:hover{
            opacity: 0.9;
        }


        @media(max-width: 1000px){

            .container{
                flex-direction: column;
                width: 95%;
                height: auto;
            }

            .left-panel,
            .right-panel{
                width: 100%;
            }

            .left-content h1{
                font-size: 50px;
            }

            .form-container h2{
                font-size: 40px;
            }

        }

    </style>

</head>

<body>

    <div class="container">

        <div class="left-panel">

            <div class="left-content">

                <h1>Get Started</h1>

                <p>Already have an account?</p>

                <a href="login.php" class="login-btn">
                    Log in
                </a>

            </div>

        </div>


        <div class="right-panel">

            <div class="form-container">

                <h2>SIGN UP</h2>

                <?php if(!empty($message)) : ?>

                    <div class="message">
                        <?php echo $message; ?>
                    </div>

                <?php endif; ?>

                <form method="POST">

            
                    <div class="form-group">
                    <label>First Name:</label>

                        <input 
                            type="text"
                            name="first_name"
                            placeholder="Enter your first name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Last Name:</label>

                        <input 
                            type="text"
                            name="last_name"
                            placeholder="Enter your last name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Email</label>

                        <input 
                            type="email" 
                            name="email"
                            placeholder="Enter your email"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Password</label>

                        <input 
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>

                        <input 
                            type="password"
                            name="confirm_password"
                            placeholder="Confirm your password"
                            required
                        >
                    </div>

                    <div class="form-group">

                        <label>Select Role</label>

                        <select name="role_id" id="roleSelect" required>

                            <option value="">Choose Role</option>
                            <option value="1">seller</option>
                            <option value="2">client</option>
                            <option value="3">admin</option>
                            

                        </select>
                    </div>
                        <div class="form-group" id="adminSecretGroup" style="display: none;">
                            <label>Admin Authentication Code</label>
                            <input 
                                type="password"
                                name="admin_confirmation"
                                placeholder="Enter admin authentication code"
                            ></input>

                    </div>

                    <button type="submit" class="signup-btn">
                        Sign up
                    </button>

                </form>

            </div>

        </div>

    </div>
<script>
    const roleSelect=document.getElementById("roleSelect");
    const adminSecretGroup=document.getElementById("adminSecretGroup");

    roleSelect.addEventListener("change", function(){
        if(this.value=="3"){
            adminSecretGroup.style.display= "block";
        } else {
            adminSecretGroup.style.display= "none";
        }
    });
</script>
</body>
</html>





