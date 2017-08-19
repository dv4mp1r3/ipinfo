<div class="row">
    <div class="widget widget-gray"> 
        <div class="widget-head">
            <h4 class="heading">
                <i class="fa {$icon}"></i>  
                {$caption}
            </h4>
        </div>
        <div class="widget-body" id="widget-{$widgetName}"> 
            {if $data !== null}
                {foreach from=$data key=k item=v}
                    <p class="name">{$k}</p>
                    <p class="value">
                        {if $v}
                            {$v}
                        {else}
                            N/A
                        {/if}
                    </p>
                {/foreach}
            {else if $widgetName eq 'screen'}
                <p class="name">Screen size</p>
                <p class="value">N/A</p>
                <p class="name">Window size</p>
                <p class="value">N/A</p>
                <p class="name">Pixel depth</p>
                <p class="value">N/A</p>
                <p class="name">Color depth</p>
                <p class="value">N/A</p>
                <p class="name">availLeft</p>
                <p class="value">N/A</p>
                <p class="name">availTop</p>
                <p class="value">N/A</p>
                <p class="name">availWidth</p>
                <p class="value">N/A</p>
                <p class="name">availHeight</p>
                <p class="value">N/A</p>
            {else if $widgetName eq 'os'}
                <p class="name">Headers</p>
                <p class="value"></p>
                <p class="name">JavaScript</p>
                <p class="value os-js"></p>
                <p class="name">Flash</p>
                <p class="value os-flash"></p>
                <p class="name">Java</p>
                <p class="value os-java"></p>
            {else if $widgetName eq 'language'}
                <p class="name">Headers</p>
                <p class="value"></p>
                <p class="name">JavaScript</p>
                <p class="value language-js"></p>
                <p class="name">Flash</p>
                <p class="value language-flash"></p>
                <p class="name">Java</p>
                <p class="value language-java"></p>
            {else if $widgetName eq 'scripts'}
                <p class="name enabled">JavaScript</p>
                <p class="value">disabled</p>
                <p class="name enabled">WebRTC</p>
                <p class="value">disabled</p>
                <p class="name disabled">ActiveX</p>
                <p class="value">disabled</p>
                <p class="name disabled">VBScript</p>
                <p class="value">disabled</p>
                <p class="name disabled">Java</p>
                <p class="value">disabled</p>
                <p class="name disabled">WebAssembly</p>
                <p class="value">disabled</p>
            {else if $widgetName eq 'time'}
                {if isset($timezone)}
                <p class="name">Zone (geoip)</p>                
                    <p class="value time-zone-geoip">{$timezone}</p>
                {/if}
            {/if}    
        </div>
        <div class="widget-footer"> 
            <a widgetName="widget-{$widgetName}"               
               id="toggle" 
               class="toggle-button fa fa-chevron-up fa-1x" 
               data-toggle="tooltip" 
               data-placement="right" 
               title="" 
               data-original-title="Show/Hide">
                <i></i>
            </a>
        </div>

    </div>
</div>
