divTickers = document.getElementById("divTickers");

var xmlhttp = new XMLHttpRequest();
var url = "php/index.php/TICKERS";

xmlhttp.onreadystatechange = function() {
    //document.writeln("<p>" + this.readyState + "</p>");
    //document.writeln("<p>" + this.status + "</p>");
    if (this.readyState == 4 && this.status == 200) {
        var arrayTickers = JSON.parse(this.responseText);

        //document.writeln(this.responseText);
        //document.writeln("<p>dentro del if</p>");
        //document.writeln(arrayTickers);
        //document.writeln(arrayTickers.estado);

        var out = "";
        var i;
        //document.writeln(arrayTickers.datos.length);
        for(i = 0; i < arrayTickers.datos.length; i++) {
            out += '<div class="spanTickers" id="' + arrayTickers.datos[i].Ticker + '">' +
            arrayTickers.datos[i].Nombre + ' (' + arrayTickers.datos[i].codPais + ')</div>';
            //document.writeln(i);
        }
        //document.writeln(out);
        divTickers.innerHTML = out;
    }
    //document.writeln("fuera del if");
};

//document.writeln("<p>Antes del open...</p>");
xmlhttp.open("GET", url, true);
//document.writeln("<p>Antes del send...</p>");
xmlhttp.send();