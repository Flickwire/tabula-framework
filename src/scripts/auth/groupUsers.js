$(function(event) {
    $(".delete").click(function(e){
        e.stopPropagation();
        e.preventDefault();
        window.deleteHref = $(e.target).attr('href');
        $('.deletemodal').modal('show');
    })

    $(".deletemodal").modal('setting', {
        onApprove: function(){
            window.location = window.deleteHref;
        }
    });
    
    var addUrl = "{{ request.getSelf({'action': 'addUser', 'gid': group.id},['uid','id']) }}&uid=";
    $('.ui.search').search({
        apiSettings: {
            url: '{{ tabulaBaseUri }}api/auth?action=findUsersForGroup&gid={{group.id}}&q={query}',
            onResponse: function(rawResponse) {
              var
                response = {
                  results : []
                }
              ;
              // translate GitHub API response to work with search
              $.each(rawResponse.results, function(index, item) {
                  console.log(item);
                response.results.push({
                  title       : item.name,
                  description : item.email,
                  url         : addUrl + item.id
                });
              });
              console.log(response);
              return response;
            }
        }
    });
});