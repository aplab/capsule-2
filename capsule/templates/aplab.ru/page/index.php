<?php use Capsule\Capsule;
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?=$this->title?></title>
        <link rel="stylesheet" href="/capsule/assets/cms/css/cssreset-min.css" media="all" />
        <link rel="stylesheet" href="/capsule/assets/website/aplab/css/style.css" media="all" />
        <script type="text/javascript" src="/capsule/assets/share/jquery/jquery-2.0.3.min.js"></script>
        
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shCore.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushPlain.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushXml.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushCss.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushPhp.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushSql.js"></script>
        <script type="text/javascript" src="/capsule/assets/share/syntaxhighlighter/scripts/shBrushJScript.js"></script>
        
        <link rel="stylesheet" href="/capsule/assets/share/syntaxhighlighter/styles/shCore.css" media="all" />
        <link rel="stylesheet" href="/capsule/assets/share/syntaxhighlighter/styles/shThemeDefault.css" media="all" />
        
        <script type="text/javascript" src="/capsule/assets/website/aplab/js/js.js"></script>
        
        <link rel="stylesheet" href="/capsule/assets/website/aplab/css/custom.css" media="all" />
        
        <meta name='yandex-verification' content='5194ed66d9e1a091' />
        <meta name="google-site-verification" content="flvN0Px1JUv0SHRH4bqSYu8t4J0ndl_ZZb_DjcD7p68" />
        <meta name="description" content="<?=$this->description?>">
        <meta name="keywords" content="<?=$this->keywords?>">
    </head>
    <body>
        <div id="wrapper">
            <div id="header" class="page-section">
                <div id="logo">
                    <a href="/">
                        <img src="/capsule/assets/website/aplab/img/logo.png">
                    </a>
                </div>
                <div id="cap">
                    tel.:89089102120
                </div>
                <div id="counters-start">
                    <?php include 'inc/liveinternet_start.php' ?>
                </div>
            </div>
            <div id="body" class="page-section">
                <div id="content">
                    <?=area('left_col')?>
                </div>
                <div id="right-col">
                    <div class="wide-text-block">
                        
                    </div>
                </div>
            </div>
            <div id="footer" class="page-section">
                <div id="f-col-one">
                    <a href="/log/">Журнал</a>
                </div>
                <div id="f-counters">
                    <div id="counter-li">
                        <?php include 'inc/liveinternet_end.php' ?>
                    </div>
                    <div id="counter-ya">
                        <?php include 'inc/yandex_metrika.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>