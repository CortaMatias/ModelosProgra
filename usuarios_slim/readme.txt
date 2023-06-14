asegurarse que se puedan reescribrir
ir al archivo de configuracion del apache(httpd.conf)
#se cambia de AllowOverride none a AllowOverride all, para slim4
<Directory />
    AllowOverride all
    Require all denied
</Directory>

establecer el .htaccess (en carpeta ./public)

para configurar el virtual host:
C:\xampp8\apache\conf\extra\httpd-vhosts.conf
***----aquí, configurar el virtual host
***----ver que el ServerName se va a usar luego.....

<VirtualHost *:80>
    ServerAdmin administrator@mail.com
    DocumentRoot "C:/xampp8/htdocs/test_slim4/public"
    ServerName api_slim4
    ErrorLog "logs/api_slim4-error.log"
    CustomLog "logs/api_slim4-access.log" common
</VirtualHost>


luego ir al virtual host de windows
C:\windows\system32\drivers\etc
***----establecer el virtual host, 127.0.0.1 con el nombre del ServerName 
(hacerlo con privilegios de administrador)


reiniciar servicio de apache

en composer.json

agregar:

"autoload" : {
    "psr-4" : {
        "app\\" : "src\",
    }
}

esto mapea las rutas, y cuando se haga referencia a 'app/' se mapea con 'src'

para aplicar los cambios, ejecutar en la consola:
composer dump-autoload


