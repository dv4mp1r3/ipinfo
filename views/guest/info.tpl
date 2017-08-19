<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Добро пожаловать, снова">
        <meta name="author" content="dv4mp1r3">
        <link href="{$www_root}/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="{$www_root}/assets/css/1-col-portfolio.css" rel="stylesheet">
        <link href="{$www_root}/assets/css/guest.css" rel="stylesheet">
        <link href="{$www_root}/assets/css/font-awesome.min.css" rel="stylesheet" media="screen">
    </head>

    <body>
        <div class="container">
            	
            {include file='views/guest/values_block.tpl' 
                widgetName='screen'
                icon='fa-desktop'
                caption='Screen'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='plugins'
                icon='fa-cube'
                caption='Plugins'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='language'
                icon='fa-cube'
                caption='Language'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='time'
                icon='fa-cube'
                caption='Time'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='dns'
                icon='fa-cube'
                caption='DNS'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='navigator'
                icon='fa-internet-explorer'
                caption='Navigator'
                data=null
            }
            
            {include file='views/guest/values_block.tpl' 
                widgetName='scripts'
                icon='fa-file-code-o'
                caption='Scripts'
                data=null
            }
            
            {if $data.http_data}
                {include file='views/guest/values_block.tpl' 
                    widgetName='http-data'
                    icon='fa-server'
                    caption='HTTP data'
                    data=$data.http_data
                }
            {/if}
 
            
            {if $user_ip_info}
                {include file='views/guest/values_block.tpl' 
                    widgetName='location'
                    icon='fa-globe'
                    caption='Location'
                    data=$user_ip_info
                }
            {/if}
            
            <div class="row">
                <iframe id="iframe" sandbox="allow-same-origin allow-scripts" style="display: none"></iframe>
            </div>
        </div>
        <script src="{$www_root}/assets/js/jquery.js"></script>
        <script src="{$www_root}/assets/js/bootstrap.min.js"></script>
        <script src="{$www_root}/assets/js/ads.js"></script>
        <script src="{$www_root}/assets/js/guest.js"></script>
    </body>

</html>