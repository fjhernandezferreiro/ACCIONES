var tickerSeleccionado;
var loteSeleccionado;

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
    //document.write(strJson);
    var arrayMovimientos = JSON.parse(strJson);

    div = document.getElementById("divDatos");
    
    //Dray table header
    var out = "<table><th>Ticker</th><th>Operación</th><th>Fecha</th><th>Lote</th><th>Cantidad</th><th>Valor U</th><th>Comisión</th><th>Ret. Orig.</th><th>Ret. Dst.</th><th>Ajuste</th><th>Cambio divisa</th><th>TOTAL</th>";
    var i;
    
    //Draw table body
    //document.write(arrayMovimientos.datos.length);
    for(i = 0; i < arrayMovimientos.datos.length; i++) {
        
        //FILTROS - BEGIN
        if (document.getElementById("COMPRAS") && document.getElementById("COMPRAS").checked && arrayMovimientos.datos[i].tipoMovimiento != 'COMPRA-ACCIONES') continue;
        if (document.getElementById("DIVIDENDOS") && document.getElementById("DIVIDENDOS").checked && arrayMovimientos.datos[i].tipoMovimiento != 'ABONO DIVIDENDO') continue;
        if (loteSeleccionado && arrayMovimientos.datos[i].lote != loteSeleccionado) continue;
        if (tickerSeleccionado && arrayMovimientos.datos[i].ticker != tickerSeleccionado) continue;
        //FILTROS - END

        out += '<tr><td class="tdTickers" id="' + arrayMovimientos.datos[i].MovimientoId + '">' + arrayMovimientos.datos[i].ticker + '</td>';
        switch (arrayMovimientos.datos[i].tipoMovimiento) {
            case 'COMPRA-ACCIONES':
                tdclase = "compra";   
            break;
            case 'ABONO DIVIDENDO':
                tdclase = "dividendo";
            break;
            case 'AMPLIACIÓN LIBERADA':
                tdclase = "ampliacion";
            break;
            case 'ACCIONESxDERECHOS':
                tdclase = "compra";
            break;
            case 'AMPLIACIÓN NO LIBERADA':
                tdclase = "ampliacion";
            break;
            case 'DERECHOSxACCIONES':
                tdclase = "compra";   
            break;
            case 'DERECHOS-SOBRANTES (VENDIDOS)':
                tdclase = "venta";   
            break;
            case 'DERECHOS-VENDIDOS':
                tdclase = "venta";   
            break;
            


        }
        out += '<td class="' + tdclase + '">' + arrayMovimientos.datos[i].tipoMovimiento + '</td>';
        out += '<td class="' + tdclase + '">' + arrayMovimientos.datos[i].fechaMovimiento + '</td>';
        out += '<td class="tdLote">' + arrayMovimientos.datos[i].lote + '</td>';
        out += '<td class="' + tdclase + '">' + arrayMovimientos.datos[i].cantidad + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].valorUnitario).toFixed(4) + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].comision).toFixed(4) + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].retencionOrigen).toFixed(4) + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].retencionDestino).toFixed(4) + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].ajuste).toFixed(4) + '</td>';
        out += '<td class="' + tdclase + '">' + Number(arrayMovimientos.datos[i].tipoCambio).toFixed(4) + '</td>';

        total = arrayMovimientos.datos[i].cantidad * arrayMovimientos.datos[i].valorUnitario + Number(arrayMovimientos.datos[i].comision) - Number(arrayMovimientos.datos[i].retencionOrigen) - Number(arrayMovimientos.datos[i].retencionDestino) + Number(arrayMovimientos.datos[i].ajuste);
        if (arrayMovimientos.datos[i].tipoMovimiento=='AMPLIACIÓN LIBERADA') total = 0; // AMPLIACIÓN LIBERADA --> TOTAL = 0
        
        out += '<td class="' + tdclase + '">' + total.toFixed(4) + ' € </td>';
    }

    out += '</table>';
        
    //alert("happen!!");
    out += "<p>Registros: " + arrayMovimientos.datos.length + "</p>";
    div.innerHTML = out;

    //EVENT LISTENERS for td elements generated - BEGIN
    tdTickers = document.getElementsByClassName("tdTickers");
    for (var i = 0; i < tdTickers.length; ++i) {
        //tdTickers[i].addEventListener("click", function(){ alert("¿Perdona?"); });
        tdTickers[i].addEventListener("click", function(){ tickerSeleccionado = this.innerHTML; filtroChange(); });
    }

    tdLotes = document.getElementsByClassName("tdLote");
    //alert(tdLotes.length);
    for (var i = 0; i < tdLotes.length; ++i) {
        //alert(tdLotes[i].innerHTML);
        tdLotes[i].addEventListener("click", function(){ loteSeleccionado = this.innerHTML; filtroChange(); });
        //tdLotes[i].addEventListener("click", function(){ seleccionarLote(tdLotes[i].innerHTML); });
        //tdLotes[i].addEventListener("click", function(){ document.getElementById("divDebug").innerHTML = this.innerHTML; });
        //tdLotes[i].addEventListener("click", function(){ alert(this.innerHTML); });
        //tdLotes[i].addEventListener("click", function(){ document.getElementById("divDebug").innerHTML = "hola"; });
        //tdLotes[i].addEventListener("click", function(){ alert("¿Perdona?"); });
    }
    //EVENT LISTENERS for td elements generated - END

    //alert("What!!");
    //document.getElementById("divDebug").innerHTML = tickerSeleccionado;
        
}


function filtroChange () {
    getDataAndFormatElement("cMovimientos",formatJsonMovimientos);
}

//EVENT LISTENERS
document.getElementById("spanMenuTickers").addEventListener("click", function(){
    getDataAndFormatElement("cTickers",formatJsonTickers);
});

document.getElementById("spanMenuMovimientos").addEventListener("click", function(){
    getDataAndFormatElement("cMovimientos",formatJsonMovimientos);
});

checkboxes = document.getElementsByClassName("css-checkbox");
for (var i = 0; i < checkboxes.length; ++i) {
    checkboxes[i].addEventListener("click", function(){ filtroChange(); });
}
