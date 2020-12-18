<html>

<head>
	<title>Register</title>
</head>

<body>
	<form action = "/api/registration" method = "post">
         <input type = "hidden" name = "_token" value = "<?php echo csrf_token() ?>">
      
         <table>
            <tr>
               <td>Google Id</td>
               <td><input type = "text" name = "googleid" /></td>
            </tr>
            
            <tr>
               <td>First Name</td>
               <td><input type = "text" name = "fname" /></td>
            </tr>
         
            <tr>
               <td>Last Name</td>
               <td><input type = "text" name = "lname" /></td>
            </tr>
         
            <tr>
               <td>Email</td>
               <td><input type = "text" name = "email" /></td>
            </tr>
         
            <tr>
               <td>Password</td>
               <td><input type = "text" name = "password" /></td>
            </tr>
         
            <tr>
               <td colspan = "2" align = "center">
                  <input type = "submit" value = "Register" />
               </td>
            </tr>
         </table>
      
      </form>
</body>
</html>