# BitBucket Team Issue Management

#api
#bitbucket


Me and the team at work make extensive use of BitBuckets issue tracker to document and track bugs, issues and 
suggestions against projects and this works really well for a single repository.

The problem comes when you want to see all the issues in a group of repositories, you can see issues from ALL 
repositories but can't then order, sort or filter them, this makes it really hard to get an overview of all the issues 
in a related group of repositories.

Fortunately BitBucket has an API and I have a bunch of spare time...

## Search, Sort and Conquer

Searching was the primary feature that we needed because what's the point in having a haystack if you can't look for the
needle.

I started by seeing if the API supported full text searches and it seemed that v2 could but not the newer v3, weird... 
This meant I was going to have to handle searching myself as I didn't want to use the older version, so I was going to 
have to fetch and store all the data.

It was a breeze to get a list of all the repositories in a team and then fetch the issues for each one, compiling them 
all into a single JSON file, the only complexity was that the issues were paginated but one self-referencing function 
later we had all the issues in one place.

## Data Tables

To display all the issues I made use of [Data Tables](https://www.datatables.net/) which while it was very easy to setup and had built in 
searching and sorting was a total pain to customise.

All I wanted to change was to make some column headings into drop downs that filtered the data based on the selected 
value and make the issue titles links back to BitBucket, both much much harder than you would think.

## Conclusion

While I wish BitBucket would implement the functionality we needed this was a good solution and a fun little project 
and one which will hopefully get better over time.

Check out the code on [GitHub](https://github.com/moebrowne/BitBucket-Team-Issue-Manger)
