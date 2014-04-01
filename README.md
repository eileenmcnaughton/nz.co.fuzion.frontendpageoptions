nz.co.fuzion.frontendpageoptions
================================

CiviCRM extension to add extra options to contribution & event pages
This extension is dependent on the entity setting extension
https://github.com/eileenmcnaughton/nz.co.fuzion.entitysetting

Options added are
1) ability to choose an alternate thank you page instead of standard CiviCRM landing page
2) ability to specify a relationship that is created when someone is registered 'on behalf' (ie. with cid=0 in the url) 
- if you want this to be permissioned you might want to use the relatedpermission extension as well

Expected additions in the future are
1) ability to make a page 'renewal only' - ie. price set options for memberships the user does not currently have 
are not displayed (based on https://github.com/eileenmcnaughton/nz.co.fuzion.renewalonlypage)
2) ability to make forms ONLY accept recurring contributions

WARNING
installing the entity settings extension can cause faulty html in core to be reported as an error - in particular there
is a problem with the event page css. I did try to fix this but the fix was not OK for core - however, I am running
the fix in our 4.4 deployment of CiviCRM. I now think the problem might be that there are <a> tags interspersed with a <span>
In any case you will see an ugly but harmless but of code at the bottom of the event pages. This is because the
entity setting extension renders the page's html in the alterContent hook & the html on the event pages is not valid in 4.4
https://issues.civicrm.org/jira/browse/CRM-14302?jql=text%20~%20%22html%22
