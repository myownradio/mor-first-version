var procStartTime = new Date().getTime();
function log(message)
{
    var d = new Date().getTime() - procStartTime;
    console.log((d / 1000).toString() + " " + message);
}