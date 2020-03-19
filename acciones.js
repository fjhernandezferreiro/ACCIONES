/*
    getDataAndFormatElement  --> Recupera los datos de la BBDD y devuelve un JSON
        data                --> Tabla de la BBDD a consultar
        formatFunction      --> Como formatear el JSON de los datos
*/
function getDataAndFormatElement (data, formatFunction) {
    var xmlhttp = new XMLHttpRequest();
    var url = "php/index.php/" + data; // "php/index.php/cTickers";
    
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //document.write("Status --> " + this.status);
            //document.write("ReadyState --> " + this.readyState);
            //document.write("responseText --> " + this.responseText);
            strJson = this.responseText; 
            formatFunction(strJson);                  
        }
    };
    
    xmlhttp.open("get", url, true); //method (get, put, post, delete, ...), url, async
    //Synchronous XMLHttpRequest (async = false) is not recommended because the JavaScript will stop executing until the server response is ready. If the server is busy or slow, the application will hang or stop.
    xmlhttp.send();
}

/*
    formatJsonTickers
        strJson     --> JSON con los datos
*/
function formatJsonTickers (strJson) {
    var arrayTickers = JSON.parse(strJson);

    div = document.getElementById("divDatos");
    
    var out = "";
    var i;
    
    for(i = 0; i < arrayTickers.datos.length; i++) {
        out += '<div class="spanTickers" id="' + arrayTickers.datos[i].tickerId + '">' +
        arrayTickers.datos[i].Nombre + ' (' + arrayTickers.datos[i].codPais + ')</div>';
    }
    div.innerHTML = out;
}

function formatJsonMovimientos (strJson) {
    var arrayMovimientos = JSON.parse(strJson);

    div = document.getElementById("divDatos");
    
    var out = "<table><th></th>";
    var i;
    
    for(i = 0; i < arrayMovimientos.datos.length; i++) {
        out += '<tr><td>';
        out += '<span class="spanTickers" id="' + arrayMovimientos.datos[i].MovimientoId + '">';
        out += arrayMovimientos.datos[i].ticker + ' (' + arrayMovimientos.datos[i].tipoMovimiento + ')</td>';
        out += '<td>' + arrayMovimientos.datos[i].fechaMovimiento + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].cantidad + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].valorUnitario + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].comision + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].retencionOrigen + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].retencionDestino + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].ajuste + '</td>';
        out += '<td>' + arrayMovimientos.datos[i].tipoCambio + '</td>';
    }
    out += '</table>';
    div.innerHTML = out;
}
document.getElementById("spanMenuTickers").addEventListener("click", function(){
    getDataAndFormatElement("cTickers",formatJsonTickers);
});

document.getElementById("spanMenuMovimientos").addEventListener("click", function(){
    getDataAndFormatElement("cMovimientos",formatJsonMovimientos);
});
