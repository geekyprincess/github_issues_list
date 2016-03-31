# github_issues_list
AIM of this project was to list the count of the issues for different user entered public git repositories according to different conditions given. 

Technologies used to create this project are : AngularJS, Twitter Bootstrap and PHP. 

The project can be viewed live at https://gitissuecounter.herokuapp.com/

The project is divided into three parts, 
    - index.html(for view),
    - controller.js(AngularJS controller which interacts with html and php), 
    - get_issues.php(which gets the input link and process to resend the answer to controller)

The user when enters a public repository's link, the application displays the issue counts according to four different conditions. 

To get the count, I have used the Github developer's api. We first find the org/repo from the link entered by the user. 
Then hit the git hub api through curl to get the header information and issues. From the headers we retrieve the number of pages, issues are spanning to (to take care of pagination)

We obtain all the issues. There can be two approaches to find issue counts according to the four conditions we have used. First is to hit the github api for each condition, which will cause multiple hit to git and other way is to get hit the github api once and do the rest of the calculations based upon the created date. The second approach as been used since it requires only one time hit to the github.

The 'created_at' key from each issue is being compares to the time constraints we have. According to the match, the counters of all the four conditions are being incremented. 

Finally the 'get_issues.php' file sends a json reponse to the angularJS controller. The controller on recieving a successful response displays the output on the index.html file in the form of table. 

