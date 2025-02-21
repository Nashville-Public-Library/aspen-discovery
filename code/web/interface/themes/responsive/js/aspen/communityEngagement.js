AspenDiscovery.CommunityEngagement = function() {
    return {
        campaignRewardGiven: function(userId, campaignId) {
            var url = Globals.path + "/Community/AJAX?method=campaignRewardGivenUpdate";
            var params = {
                userId: userId, 
                campaignId: campaignId,
            };
            $.getJSON(url, params, 
                function(data) {
                    if (data.success) {
                        var button = $('.set-reward-btn[data-user-id="' + userId + '"][data-campaign-id="' + campaignId + '"]');
                        button.replaceWith('<span>Reward Given</span>');
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown){
               
                alert('An error occurred while updating the reward status.' + textStatus + ', ' + errorThrown);
                });
        },
        milestoneRewardGiven: function(userId, campaignId, milestoneId) {
            var url = Globals.path + "/Community/AJAX?method=milestoneRewardGivenUpdate";
            var params = {
                userId: userId,
                campaignId: campaignId,
                milestoneId: milestoneId,
            };
            $.getJSON(url, params,
                function(data) {
                    if (data.success) {
                        var button = $('.set-reward-btn-milestone[data-user-id="' + userId + '"][data-campaign-id="' + campaignId + '"][data-milestone-id="' + milestoneId + '"]');
                        button.replaceWith('<span>Milestone Reward Given</span>');
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: " + textStatus + ", " + errorThrown);
                    console.error("Response Text: " + jqXHR.responseText);
                    alert('An error occurred while updating the reward status for this milestone.' + textStatus + ', ' + errorThrown);
                });
        },
        filterDropdownOptions: function(filterType) {
            console.log("Filter Type: " + filterType);

           var selectedId = (filterType === 'campaign') ? document.getElementById("campaign_id").value : document.getElementById("user_id").value;

           console.log("Selected ID: " + selectedId);
            var url = Globals.path + "/Community/AJAX?method=filterCampaigns";
            var params = {
                campaignId: filterType === "campaign" ? selectedId : null,
                userId: filterType === "user" ? selectedId : null
            }
            console.log("Params: " +  params);
            
            //Show/hide campaigns list and filtered campaigns divs
            var campaignsList = document.getElementById("campaignsList");
            var filteredCampaign = document.getElementById("filteredCampaign");

     
            $.getJSON(url, params, 
                function(data) {
                    console.log(data);
                    if (data.success) {
                        $('#filteredCampaign').html(data.html);
                        filteredCampaign.style.display = "block"; 
                        campaignsList.style.display = "none";
                    } else {
                        alert("Error:" +  data.message);
                    }
                })
                .fail(function() {
                    console.error('Error retrieving campaign data.');
                });
        
        }
    }
    
}(AspenDiscovery.CommunityEngagement || {});