FROM johanmeijdam/cubetoolbase
RUN ls -ltr
RUN mkdir Systems
COPY CubeGen.pl Systems
RUN chmod +x /Systems/CubeGen.pl

RUN mkdir Systems/CubeTool
COPY PgTest.sh Systems/CubeTool
COPY CubeTool.sh Systems/CubeTool
COPY Makefile Systems/CubeTool

COPY CubeBoModel.cgm Systems/CubeTool

RUN mkdir git
RUN git clone https://github.com/JohanMeijdam/CubeTool /git

#RUN echo "Docker CompoSYS" > /var/www/html/index.html
COPY www /var/www/html
RUN mv /var/www/html/CubeTool/scripts Systems/CubeTool
RUN pwd
RUN ls -ltr Systems/CubeTool
EXPOSE 80
CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]

