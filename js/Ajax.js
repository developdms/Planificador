/*global alert, XMLHttpRequest*/
"use strict";
function Ajax() {
    this.peticion = new XMLHttpRequest();
    this.url = "";
    this.parametros = "";
    this.metodo = "GET";
    this.xml = false;
    this.respuesta = alert;
    this.error = alert;
}
Ajax.prototype.doPeticion = function () {
    var that = this;
    var procesa = function (funcion, dato) {
        funcion(dato);
    };
    this.peticion.open(this.metodo, this.url, true);
    this.peticion.onreadystatechange = function () {//onload
        if (that.peticion.readyState === 4) {//0-unsent, 1-opened, 2-headers_received, 3-loading, 4-done
            if (that.peticion.status === 200) {//200-ok
                var respuesta;
                if (that.xml) {
                    respuesta = that.peticion.responseXML;
                } else {
                    respuesta = that.peticion.responseText;
                    //respuesta = JSON.parse(that.peticion.responseText);
                }
                procesa(that.respuesta, respuesta);
            } else {
                procesa(that.error, "error");
            }
        }
    };
    this.peticion.responseType = "";
    //"arraybuffer", "blob", "document", "json", "text", "" (default) //"json" -> this.peticion.response
    if (this.metodo === "POST") {
        this.peticion.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.peticion.send(this.parametros);
    } else {
        this.peticion.send();
    }
};
Ajax.prototype.setGet = function () {
    this.metodo = "GET";
};
Ajax.prototype.setJSON = function () {
    this.xml = false;
};
Ajax.prototype.setParametros = function (params) {
    this.parametros = params;//name=encodeURIComponent(value)&
};
Ajax.prototype.setPost = function () {
    this.metodo = "POST";
};
Ajax.prototype.setRespuesta = function (funcion) {
    this.respuesta = funcion;
};
Ajax.prototype.setRespuestaError = function (funcion) {
    this.error = funcion;
};
Ajax.prototype.setUrl = function (url) {
    this.url = url;
};
Ajax.prototype.setXML = function () {
    this.xml = true;
};
Ajax.prototype.getCabeceras = function () {
    return this.peticion.getAllResponseHeaders();
};
Ajax.prototype.getContentType = function () {
    return this.peticion.getResponseHeader("content-type");
};


