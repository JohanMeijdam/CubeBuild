FROM johanmeijdam/cubetoolbase

COPY Systems /root/Systems
COPY html /var/www/html

EXPOSE 80

CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]

