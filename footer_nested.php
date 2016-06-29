<script>
    /*
    if(document.getElementById('loginForm')){

        //-=-=- Don't set/start session timers when on login form
        setLoginFieldFocus();
    }
    else{

        document.onkeypress = resetTimer;
        document.onmousemove = resetTimer;
    }
    */
    if(self != top){ //-=-=-=- auto resize nested iframes

        var body = document.body, html = document.documentElement;

        var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

        parent.document.getElementById(window.name).style.height = height + "px";
        parent.document.getElementById(window.name).parentNode.style.height = height + "px";
    }
</script>
</body>
</html>
