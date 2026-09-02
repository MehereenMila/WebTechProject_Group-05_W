<?php 
echo "Admin password hash: " . password_hash("admin24", PASSWORD_DEFAULT) . "<br><br>";
echo "Manager password hash: " . password_hash("manager123", PASSWORD_DEFAULT);
?>