var app = angular.module("mp3Convert", ["checklist-model"]);

app.controller("downloadsController", function($scope, $http, $interval) {

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

//     $scope.checkAll = function(e) {
//       e.preventDefault();
//       $scope.toDownload = $scope.availableFiles.map(function(item) item.filepath);
//       $scope.status = "All files selected.";
//     };
// 
//     $scope.checkNone = function(e) {
//       e.preventDefault();
//       $scope.toDownload = [];
//       $scope.status = "Files to download cleared.";
//     };
    
//     $scope.download = function() {
//       $http({
//         method: 'GET',
//         url: 'do_it.php',
//         params: {
//           action: 'download_files',
//           "files[]": $scope.toDownload
//         }
// //       }).success(function() {
// //         $scope.status = "Files successfully downloaded.";
// //       }).error(function() {
// //         $scope.status = "There was a problem.";
//       });
//     };      
  });
        