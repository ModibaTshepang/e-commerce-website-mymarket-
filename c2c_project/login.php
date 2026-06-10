<?php
  session_start();
  include "config/database.php";
  $message="";

  if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $email=trim($_POST["email"]);
    $password=$_POST["password"];

    $query="select*from users where email=?";
    $stmt=mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result=mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result)>0){
        $user=mysqli_fetch_assoc($result);

        if(password_verify($password, $user["password"])){
            $_SESSION["user_id"]=$user["user_id"];
            $_SESSION["first_name"]=$user["first_name"];
            $_SESSION["role_id"]=$user["role_id"];

            if ($user["role_id"]==3){
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($user["role_id"]==1){
                header("Location: seller-homepage.php");
                exit();
            } else{
                header("Location: index.php");
                exit();
            }
        } else{
            $message="Incorrect email or password";
        }

    } else{
        $message="Incorrect email or password";
    }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <title>Login page</title>

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
            width: 1000px;
            height: 600px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
        }

        .left-panel{
            width: 50%;
            background: linear-gradient(180deg, #3525ff, #2417d8);
            color: white;
            padding: 60px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-panel::before{
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            bottom: -150px;
            left: -100px;
        }

        .left-panel h1{
            font-size: 60px;
            margin-bottom: 20px;
            z-index: 1;
        }

        .left-panel p{
            font-size: 24px;
            margin-bottom: 30px;
            z-index: 1;
        }

        .register-btn{
            display: inline-block;
            width: fit-content;
            padding: 15px 50px;
            border: 2px solid white;
            border-radius: 40px;
            color: white;
            text-decoration: none;
            font-size: 22px;
            transition: 0.3s;
            z-index: 1;
        }

        .register-btn:hover{
            background: white;
            color: #3525ff;
        }

        .right-panel{
            width: 50%;
            background: #f7f7f7;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel h2{
            text-align: center;
            color: #4c46ff;
            font-size: 40px;
            margin-bottom: 40px;
        }

        .message{
            background: #ffdede;
            color: #b30000;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .form-group{
            margin-bottom: 20px;
        }

        .form-group label{
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .form-group input{
            width: 100%;
            padding: 14px;
            border: 2px solid #ccc;
            border-radius: 12px;
            outline: none;
            font-size: 15px;
        }

        .form-group input:focus{
            border-color: #4c46ff;
        }

        .login-btn{
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(90deg, #4c46ff, #5f5fff);
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover{
            opacity: 0.9;
        }

        @media(max-width: 900px){

            .container{
                flex-direction: column;
                width: 95%;
                height: auto;
            }

            .left-panel,
            .right-panel{
                width: 100%;
            }

            .left-panel h1{
                font-size: 45px;
            }

        }

    </style>

</head>

<body>

    <div class="container">
        <div class="left-panel">
            <h1>Welcome Back</h1>
            <p>
                Don't have an account yet?
            </p>
            <a href="register.php" class="register-btn">
                Register
            </a>

        </div>

        <div class="right-panel">

            <h2>LOGIN</h2>

            <?php if(!empty($message)) : ?>

                <div class="message">
                    <?php echo $message; ?>
                </div>

            <?php endif; ?>

            <form method="POST">

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

                <button type="submit" class="login-btn">
                    Login
                </button>

            </form>

        </div>

    </div>

</body>
</html>
