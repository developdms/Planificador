
function dateElements() {
    var week = getActualWeek();
    var elements = document.getElementById('calendario');
    for (var i = 0; i < week.length; i++) {
        var container = document.createElement('div');
        container.classList.add('weekday');
        var h3 = document.createElement('h3');
        h3.classList.add('date');
        h3.textContent = week[i] + ': ';
        container.appendChild(h3);
        var span = document.createElement('span');
        span.classList.add('blue');
        h3.appendChild(span);
        var table = document.createElement('table');
        var tr = document.createElement('tr');
        table.appendChild(tr);
        hourElements(tr);
        container.appendChild(table);
        elements.appendChild(container);
    }

}

function hourElements(append) {
    var hours = new Array('10:00h', '11:00h', '12:00h', '13:00h', '14:00h', '15:00h', '16:00h', '17:00h', '18:00h', '19:00h', '20:00h', '21:00h', '22:00h');
    for (var x = 0; x < hours.length; x++) {
        var td = document.createElement('td');
        td.classList.add("hour", "green");
        td.textContent = hours[x];
        append.appendChild(td);
    }
}

function modalMessage(value, text) {
    var message = document.getElementById('message');
    var control = document.getElementById('control');
    var close = document.getElementById('cerrar');
    message.textContent = text;
    if (value == 1) {
        message.classList.add('letterGreen');
        control.classList.add('hidden');
        close.classList.remove('hidden');
    } else if (value == 2) {
        message.classList.add('letterRed');
        control.classList.add('hidden');
        close.classList.remove('hidden');
    } else {
        message.classList.remove('letterGreen', 'letterRed');
        control.classList.remove('hidden');
        close.classList.add('hidden');
    }
}

function allGreen() {
    var items = document.getElementsByClassName('hour');
    for (var i = 0; i < items.length; i++) {
        items[i].classList.remove('red');
        items[i].classList.add('green');
    }
}

function getInsertConfirmMessage() {
    return confirm('Vas a realizar la reserva\n¿Estás seguro?');
}

function getLang() {
    return navigator.language || navigator.userLanguage;
}

function getWeekDay() {
    var d = new Date();
    return d.getDay();
}

function getWeekLanguage() {
    var week;
    switch (getLang()) {
        case "fr":
            week = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            break;
        case "it":
            week = new Array('Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato');
            break;
        case "en":
            week = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            break;
        default:
            week = new Array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    }
    return week;
}

function getActualWeek() {
    var week = getWeekLanguage();
    var actual = new Array();
    for (var i = 0; i < 7; i++) {
        actual.push(week[(getWeekDay() + i) % 7]);
    }
    return actual;
}

function addZero(param) {
    if (param < 10) {
        return '0' + param;
    }
    return param;
}

function today(param) {
    var day = new Date();
    if (param != null) {
        day.setDate(day.getDate() + param);
    }
    return day.getFullYear() + '-' + addZero((day.getMonth() + 1)) + '-' + day.getDate();
}

function setDatesTo() {
    var items = document.getElementsByClassName('date');
    for (var i = 0; i < items.length; i++) {
        items[i].setAttribute('data-date', today(i));
        var date = new Date(today(i));
        items[i].getElementsByTagName('span')[0].textContent = addZero(date.getDate()) + '/' + addZero(date.getMonth()) + '/' + date.getFullYear();
    }
}

function setHoursTo() {
    var items = document.getElementsByClassName('hour');
    for (var i = 0; i < items.length; i++) {
        items[i].setAttribute('data-hour', items[i].textContent.substring(0, (items[i].textContent.length - 1)) + ':00');
    }
}

function colors(element, addColor, removeColor) {
    element.classList.remove(removeColor);
    element.classList.add(addColor);
}

function dialog(element) {
    var date = element.parentNode.parentNode.previousElementSibling.getAttribute('data-date');
    var hour = element.getAttribute('data-hour');
    document.getElementById('fecha').value = date;
    document.getElementById('hora').value = hour;
    document.getElementById("myDialog").showModal();
}

function closeDialog() {
    document.getElementById("myDialog").close();
    modalMessage(0, 'Acciones');
}

function ajaxRequest(url, response, params) {
    var ajax = new Ajax();
    ajax.setUrl(url);
    ajax.setPost();
    ajax.setRespuesta(response);
    ajax.setParametros(params);
    ajax.doPeticion();
}

/*
 * --------------------------------------
 * RESPUESTA OPERACIONES
 * --------------------------------------
 */

function getBookingNumberResponse(items) {
    var res = JSON.parse(items);
    return res.length;
}

function bookingDayResponse(param) {
    var res = JSON.parse(param);
    var list = document.getElementById('booking');
    list.innerHTML = "";
    for (var i = 0; i < res.length; i++) {
        var p = document.createElement('p');
        p.textContent = res[i].user;
        list.appendChild(p);
    }
}

function readListResponse(items) {
    var res = JSON.parse(items);
    var elements = document.getElementsByClassName('date');
    for (var n = 0; n < res.length; n++) {
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].getAttribute('data-date') == res[n].date) {
                var cells = elements[i].nextElementSibling.getElementsByTagName('td');
                for (var x = 0; x < cells.length; x++) {
                    if (cells[x].getAttribute('data-hour') == res[n].hour) {
                        colors(cells[x], 'red', 'green');
                    }
                }
            }
        }
    }
}

function insertBookingResponse(item) {
    var res = JSON.parse(item);
    if (res.res == '1') {
        var elements = document.getElementsByClassName('date');
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].getAttribute('data-date') == res.date) {
                var cells = elements[i].nextElementSibling.getElementsByTagName('td');
                for (var x = 0; x < cells.length; x++) {
                    if (cells[x].getAttribute('data-hour') == res.hour) {
                        colors(cells[x], 'red', 'green');
                    }
                }
            }
        }
        modalMessage(1, 'Correcto');
    } else {
        modalMessage(2, 'Error');
    }
}

function deleteBookingResponse(item) {
    var res = JSON.parse(item);
    if (res.res == '1') {
        modalMessage(1, 'Correcto');
        allGreen();
        readList();
    } else {
        modalMessage(2, 'Error');
    }
}

function getLoginResponse(param) {
    var res = JSON.parse(param);
    if (res.res == 0) {
        modalMessage(2, 'Usuario o password incorrectos');
        document.getElementById('myDialog').showModal();
    } else if (res.res == -1) {
        modalMessage(2, 'Usuario bloqueado.\n Contacte con el administrador');
        document.getElementById('myDialog').showModal();
    } else {
        calendar();
    }
}

function getSignUpResponse(param) {
    var res = JSON.parse(param);
}

/*
 * --------------------------------------
 * FIN RESPUESTA OPERACIONES
 * --------------------------------------
 */

/*
 * --------------------------------------
 * OPERACIONES
 * --------------------------------------
 */

function getBookingNumber(date, hour) {
    var ajax = new Ajax();
    ajax.setUrl('controller.php');
    ajax.setPost();
    ajax.setRespuesta(getBookingNumberResponse);
    ajax.setParametros('do=get&set=BookingNumber&date=' + date + '&hour=' + hour);
    ajax.doPeticion();
}

function insertBooking(date, hour) {
    if (getInsertConfirmMessage()) {
        if (date != null && date != '' && hour != null && hour != '') {
            ajaxRequest('controller.php', insertBookingResponse,
                    'op=insert&set=Calendar&date=' + date + '&hour=' + hour);
        }
    }
}

function deleteBooking(date, hour) {
    if (date != null && date != '' && hour != null && hour != '') {
        ajaxRequest('controller.php', deleteBookingResponse,
                'op=delete&set=Calendar&date=' + date + '&hour=' + hour);
    }
}

function readList() {
    ajaxRequest('controller.php', readListResponse, 'op=get&set=Calendar');
}

function bookingDay(element) {
    var date = element.parentNode.parentNode.previousElementSibling.getAttribute('data-date');
    var hour = element.getAttribute('data-hour');
    if (date != null && date != '' && hour != null && hour != '') {
        ajaxRequest('controller.php', bookingDayResponse,
                'op=get&set=BookingDay&date=' + date + '&hour=' + hour);
    }
}

function getLogin() {
    var user = document.getElementById('alias').value;
    var pass = document.getElementById('pass').value;
    ajaxRequest('controller.php', getLoginResponse,
            'op=get&set=Login&alias=' + user + '&password=' + pass);
}

function getSignUp() {
    var user = document.getElementById('alias').value;
    var pass = document.getElementById('pass').value;
    ajaxRequest('controller.php', getSignUpResponse,
            'op=get&set=SignUp=' + user + '&password=' + pass);
}

/*
 * --------------------------------------
 * FIN OPERACIONES
 * --------------------------------------
 */

/*
 * --------------------------------------
 * ASIGNACIONES DE EVENTOS
 * --------------------------------------
 */

function setEvent(element, event, handler) {
    if (element.length > 0) {
        for (var i = 0; i < element.length; i++) {
            element[i].addEventListener(event, handler, false);
        }
    } else {
        element.addEventListener(event, handler, false);
    }
}

function eventsCalendar() {
    var hours = document.getElementsByClassName('hour');
    setEvent(hours, 'mousedown', function(e) {
        if (e.which == 1) {
            bookingDay(this);
            //insertBooking(this);
        } else {
            dialog(this);
            //deleteBooking(this);
        }
    });
    readList();
    setDatesTo();
    setHoursTo();
}

function eventsSignUp(param) {
    var btSignup = document.getElementById('btsignup');
    var btBack = document.getElementById('btback');
    if(param==1){
        setEvent(btSignup,'click',setSignUp);
    }else{
        setEvent(btSignup,'click',setEdit);
    }
    setEvent(btBack,'click',setBack);
}

function eventsLogin() {
    var btLogin = document.getElementById('btlogin');
    setEvent(btLogin, 'click', getLogin);
}

/*
 * --------------------------------------
 * FIN ASIGNACIONES DE EVENTOS
 * --------------------------------------
 */

/*
 * --------------------------------------
 * RESPUESTA FUNCTIONES DE VISTA
 * --------------------------------------
 */

function setSignUp(){
    
}

function setEdit(){
    
}

function setBack(){
    calendar();
}

function loginResponse(param) {
    var wrapper = document.getElementById('wrapper');
    wrapper.innerHTML = param;
    eventsLogin();
}

function signupResponse(param) {
    eventsSignUp();
}

function calendarResponse(param) {
    var wrapper = document.getElementById('wrapper');
    wrapper.innerHTML = param;
    dateElements();
    setDatesTo();
    setHoursTo();
    eventsCalendar();
    readList();
    manageEvents();
}

function viewNewUserResponse(param) {
    var wrapper = document.getElementById('wrapper');
    wrapper.innerHTML = param;
    eventsSignUp(1);
}

function viewEditUserResponse(param) {
    var wrapper = document.getElementById('wrapper');
    wrapper.innerHTML = param;
    eventsSignUp(2);
}

function viewDeleteUserResponse(param) {
    var wrapper = document.getElementById('wrapper');
    wrapper.innerHTML = param;
}

function exitUserResponse(param) {
    loginResponse(param);
}

/*
 * --------------------------------------
 * FIN RESPUESTA FUNCTIONES DE VISTA
 * --------------------------------------
 */

/*
 * --------------------------------------
 * FUNCIONES DE VISTA
 * --------------------------------------
 */

function calendar() {
    ajaxRequest('controller.php', calendarResponse, 'op=view&set=Calendar');
}

function login() {
    ajaxRequest('controller.php', loginResponse, 'op=view&set=Login');
}

function signup() {
    ajaxRequest('controller.php', signupResponse, 'op=view&set=SignUp');
}

function viewNewUser() {
    ajaxRequest('controller.php', viewNewUserResponse, 'op=view&set=NewUser');
}

function viewEditUser() {
    ajaxRequest('controller.php', viewEditUserResponse, 'op=view&set=EditUser');
}

function viewDeleteUser() {
    ajaxRequest('controller.php', viewDeleteUserResponse, '');
}

function exitUser() {
    ajaxRequest('controller.php', exitUserResponse, 'op=exit&set=User');
}

/*
 * --------------------------------------
 * FIN FUNCIONES DE VISTA
 * --------------------------------------
 */

function manageEvents() {
    if (document.getElementById('ed') != null) {
        setEvent(document.getElementById('ed'), 'click', viewEditUser);
        setEvent(document.getElementById('de'), 'click', viewDeleteUser);
        setEvent(document.getElementById('ex'), 'click', exitUser);
    }
    if (document.getElementById('ne')) {
        setEvent(document.getElementById('ne'), 'click', viewNewUser);
    }
}

function main() {
    setEvent(document.getElementById('reservar'), 'click', function() {
        var date = document.getElementById('fecha').value;
        var hour = document.getElementById('hora').value;
        insertBooking(date, hour);
    });
    setEvent(document.getElementById('eliminar'), 'click', function() {
        var date = document.getElementById('fecha').value;
        var hour = document.getElementById('hora').value;
        deleteBooking(date, hour);
    });
    setEvent(document.getElementById('cancelar'), 'click', closeDialog);
    setEvent(document.getElementById('btcerrar'), 'click', closeDialog);
    if (document.getElementsByClassName('calendario').length != 0) {
        calendar();
    } else if (document.getElementsByClassName('signup').length != 0) {
        signup();
    } else {
        login();
    }
}

window.addEventListener('load', main, false);






