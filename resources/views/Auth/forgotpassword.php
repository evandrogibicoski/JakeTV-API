<html>

<head>
	<title>Forgot Password</title>
</head>

<body>
   
	<form action = "/api/forgotpassword" method = "get">
         <input type = "hidden" name = "_token" value = "<?php echo csrf_token() ?>">
      
         <table>
            <tr>
               <td>Email</td>
               <td><input type = "text" name = "email" /></td>
            </tr>
         
            <tr>
               <td colspan = "2" align = "center">
                  <input type = "submit" value = "Send" />
               </td>
            </tr>
         </table>
      
      </form>
</body>
</html>