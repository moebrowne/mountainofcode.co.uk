
Me and a team at work make extensive use of BitBuckets issue tracker to document and track bugs, issues and suggestions for projects and this works really well for a single repository.
 
The problem comes when you want to see all the issues in a group of repositories, you can see issues from ALL repositories but can't  then order or filter them, this makes it really hard to get an overview of all the issues in a related group of repositories.

Fortunately they have an API and I have a bunch of spare time...

<!-- more -->

## Search, Sort and Conquer

Searching was the primary feature that we needed as what's the point in having a haystack if you can't find the needle.

I started by seeing if the API supported full text searches and it seemed that v2 could but not v3, weird... This meant I was going to have to handle searching myself as I didn't want to use the older version, this meant I needed all the data all at once locally.

It was a breeze to get a list of all the repositories in a team and then fetch the issues for each repository and compile them into a single JSON file, the only complexity was that the issues were paginated by a self-referencing function later we had all the issues in JSON.
