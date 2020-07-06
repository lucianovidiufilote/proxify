## PREREQUISITES(TO BE UPDATED):

#### INSTALL SUPERVISOR
#### CONFIGURE SUPERVISOR(PART 1): sudo gedit /etc/supervisor/conf.d/messenger-worker.conf
`
[program:messenger-consume]
command=php /home/lucian/personal/proxify/bin/console messenger:consume job_transport -vv --time-limit=3600
user=root
numprocs=3
startsecs=0
autostart=true
autorestart=true
process_name=%(program_name)s_%(process_num)02d
`

#### CONFIGURE SUPERVISOR(PART 2): sudo gedit /etc/supervisor/supervisord.conf
`
[supervisord]
logfile=/var/log/supervisor/supervisord.log ; (main log file;default $CWD/supervisord.log)
pidfile=/var/run/supervisord.pid ; (supervisord pidfile;default supervisord.pid)
childlogdir=/var/log/supervisor            ; ('AUTO' child log dir, default $TEMP)

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[inet_http_server]
port = 127.0.0.1:9001
username = user
password = 123

[supervisorctl]
serverurl=http://localhost:9001;

[include]
files = /etc/supervisor/conf.d/*.conf
`


### How to test:
To generate 100 valid URLS:
    http://proxify.local/generate?url=http://google.com&times=100
 
To generate 100 invalid URLS:
    http://proxify.local/generate?url=http://invalid&times=100

sudo systemctl start supervisor

To see job status: 
    http://proxify.local/status
    http://localhost:9001/
    user / 123
    
Then generate other sets of URLS and see the jobs getting done!