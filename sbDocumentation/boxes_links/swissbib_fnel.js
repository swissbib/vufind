/*
 swissbib-fnel.js
 JavaScript fuer Neuerwerbungslistenabfrage aus einer Webseite auf swissbib Basel Bern
 05.05.2014 / andreas.bigger@unibas.ch
 23.03.2017 / basil.marti@unibas.ch / Anpassung für DDC

 Die Funktion fnel erwartet mindestens einen Bibliothekscode (vierstellig)
 und keinen oder beliebig viele Fachcodes (zweistellig) oder keinen oder
 beliebig viele DDC-Notationen (drei- oder fünfstellig)
 Diese werden beim Aufruf hintereinandergeschrieben (Reihenfolge beliebig) und mit + getrennt.
 z.B. javascript:fnel('A138') oder javascript:fnel('A100+is+or+tu+300+310.5')

 Wichtig: Der Bibliothekscode muss demjenigen entsprechen, der in der Exemplarnotiz als NELA1001405 vorkommt!
 Der effektive Bibliothekscode im Exemplarsatz wird von der Suche ignoriert.
 */

function fnel( arg ) {
    var arg=decodeURI(arg)
    var baseurl ="http://baselbern.swissbib.ch/Search/Results?";
    var fachcodes = new Array();
    var libraries = new Array();
    var ddc = new Array();
    var ccl = "";

    // Aufsplitten der Argumente aufgrund ihrer Laenge

    var argsplit = arg.split("+");
    for (var i = 0; i < argsplit.length; i++) {
        if ( argsplit[i].length == 4 ) {
            libraries.push(argsplit[i]);
            continue;
        } else if ( argsplit[i].length == 2 ) {
            fachcodes.push(argsplit[i]);
            continue;
        } else if ( argsplit[i].length == 3 ) {
            ddc.push(argsplit[i]);
            continue;
        } else if ( argsplit[i].length == 5 ) {
            ddc.push(argsplit[i]);
            continue;
        } else {
            alert("Vermutlich falscher Parameter:\n" + argsplit[i]);
            return;
        }
    }

    if ( libraries.length == 0 ) {
        alert("Kein Bibliothekscode angegeben!");
        return;
    }

    // Aufrufen einer Funktion je nach Art der mitglieferten Argumente

    if ( libraries.length == 1 && fachcodes.length == 0 && ddc.length == 0) {
        ccl = "type=wnel&lookfor=nel" + libraries[0] + make_date();
    } else if (ddc.length == 0) {
        ccl = "join=AND" + make_nel(libraries) + make_wfc(fachcodes) ;
    } else if (fachcodes.length == 0) {
        ccl = "join=AND" + make_nel(libraries) + make_ddc(ddc) ;
    } else {
        ccl = "join=AND" + make_nel(libraries) + make_wfc(fachcodes) + make_ddc_2(ddc) ;
    }

    myurl = baseurl + ccl;
    window.open(myurl,"_blank");
}

// ============= Diverse Hilfsfunktionen

// Hilfsfunktion fuer Datum

function make_date() {
    // construct date prev month as "yymm"
    var now=new Date();
    var date;
    var year=now.getYear();
    if ( year < 1900) year+=1900;
    var month=now.getMonth();
    if ( month == 0 ) {
        month=12;
        year--;
    }
    year -= 2000;
    if ( year < 10 ) {
        date = "0" + year;
    }
    if ( year == 0 ) {
        date = "00";
    }
    else {
        date = year;
    }
    if ( month < 10 ) { date += "0" + month; }
// Achtung: "" ist noetig, damit year und month nicht summiert werden !
    else { date += "" + month; }

    return date;
}

// Hilfsfunktionen fuer komplexe Anbfragen

// Abfragen nach Fachcode (wfc)

function make_wfc(searcharray) {
    var cclterm = "";

    for (var i = 0; i < searcharray.length; i++) {
        cclterm += "&bool1[]=OR&lookfor1[]=" + encodeURI(searcharray[i]) + "&type1[]=wfc";
    }

    return cclterm;
}

// Abfragen nach DDC (an zweiter Position)

function make_ddc(searcharray) {
    var cclterm = "";

    for (var i = 0; i < searcharray.length; i++) {
        cclterm += "&bool1[]=OR&lookfor1[]=" + encodeURI(searcharray[i]) + "*&type1[]=ddc";
    }

    return cclterm;
}

// Abfragen nach DDC (an dritter Position)

function make_ddc_2(searcharray) {
    var cclterm = "";

    for (var i = 0; i < searcharray.length; i++) {
        cclterm += "&lookfor1[]=" + encodeURI(searcharray[i]) + "*&type1[]=ddc";
    }

    return cclterm;
}

// Abfragen nach NELA-Codes

function make_nel(searcharray) {
    var cclterm = "";

    for (var i = 0; i < searcharray.length; i++) {
        cclterm += "&bool0[]=OR&lookfor0[]=nel" + encodeURI(searcharray[i]) + make_date() + "&type0[]=wnel";
    }

    return cclterm;
}