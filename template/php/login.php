<?php 
if (!isset($_GET['campaign'])) { 
    
    if (!isset($_SESSION['auth'])) { 
        
?>
    <form method="post" id="loginForm">
        <table>
            <tr>
                <td align="right"><label for="username">Username</label></td>
                <td><input type="text" name="username" id="username" /></td>
            </tr>
            <tr>
                <td align="right"><label for="password">Password</label></td>
                <td><input type="password" name="password" id="password" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Login" /></td>
            </tr>
        </table>
    </form>
<?php
    }
}
?>