

var app = angular.module('calendar', []);
app.controller('myCtrl', function($scope) {
    $scope.week = getActualWeek();
    $scope.hours = new Array('10:00h', '11:00h', '12:00h', '13:00h', '14:00h', '15:00h', '16:00h', '17:00h', '18:00h', '19:00h', '20:00h', '21:00h', '22:00h');
})
        .directive('week-day', function() {
            return {
                restrict: 'E',
                templateUrl: 'view/item.html'
            };
        });
