function filterAJAXResponce(responce)
{
    try
    {
        var jsonDATA = JSON.parse(responce);
        if(jsonDATA.error === undefined)
        {
            return jsonDATA;
        }
        if(jsonDATA.error === "ERROR_UNAUTHORIZED")
        {
            redirectLogin();
            return jsonDATA;
        }
    }
    catch(e)
    {
        myMessageBox("Wrong server responce: " . responce);
    }
}