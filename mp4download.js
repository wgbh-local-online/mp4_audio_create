var app = angular.module("mp3Convert", ["checklist-model"]);

app.controller("downloadsController", function($scope, $http, $interval) {

    $scope.availableFiles = {};    
    $scope.toDownload = [];
  
    $scope.updateFileList = function() {
      $http.get('do_it.php?action=get_downloadable_files')
        .success(function(data) {
          $scope.availableFiles = data;
         });
    };
    
    $interval( function() { $scope.updateFileList(); }, 5000);
    
    $scope.clearFiles = function(e) {
      $scope.toDownload = [];
      $http.get('do_it.php?action=clear_files')
        .success(function(data) {
          $scope.updateFileList();
          $scope.status = "Audio files cleared.";
        });      
    };
  });
        