<h2>Log in to record your progress</h2>
<?php
//show and clear any errors
if(isset($_SESSION['error'])) {
    echo $_SESSION['error'];
    $_SESSION['error'] = null;
}
?>
<form method="post" action="">
    Username: <input name ="username" type ="text"/><br/>
    Password: <input name ="password" type ="password"/><br/>
    <button>login</button>
</form>

