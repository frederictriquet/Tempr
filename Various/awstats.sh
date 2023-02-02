#!/bin/bash
cd /mnt/ramdisk
rm *.html
cat ws.access.log.14 ws.access.log.13 ws.access.log.12 ws.access.log.11 ws.access.log.10 ws.access.log.9 ws.access.log.8 ws.access.log.7 ws.access.log.6 ws.access.log.5 ws.access.log.4 ws.access.log.3 ws.access.log.2 ws.access.log.1 ws.access.log > all.log


sudo awstats -config=ws.preprod.tempr.me -output -staticlinks > all.html
sudo awstats -config=ws.preprod.tempr.me -output=alldomains -staticlinks > awstats.ws.preprod.tempr.me.alldomains.html
sudo awstats -config=ws.preprod.tempr.me -output=allhosts -staticlinks > awstats.ws.preprod.tempr.me.allhosts.html
sudo awstats -config=ws.preprod.tempr.me -output=lasthosts -staticlinks > awstats.ws.preprod.tempr.me.lasthosts.html
sudo awstats -config=ws.preprod.tempr.me -output=unknownip -staticlinks > awstats.ws.preprod.tempr.me.unknownip.html
sudo awstats -config=ws.preprod.tempr.me -output=alllogins -staticlinks > awstats.ws.preprod.tempr.me.alllogins.html
sudo awstats -config=ws.preprod.tempr.me -output=lastlogins -staticlinks > awstats.ws.preprod.tempr.me.lastlogins.html
sudo awstats -config=ws.preprod.tempr.me -output=allrobots -staticlinks > awstats.ws.preprod.tempr.me.allrobots.html
sudo awstats -config=ws.preprod.tempr.me -output=lastrobots -staticlinks > awstats.ws.preprod.tempr.me.lastrobots.html
sudo awstats -config=ws.preprod.tempr.me -output=urldetail -staticlinks > awstats.ws.preprod.tempr.me.urldetail.html
sudo awstats -config=ws.preprod.tempr.me -output=urlentry -staticlinks > awstats.ws.preprod.tempr.me.urlentry.html
sudo awstats -config=ws.preprod.tempr.me -output=urlexit -staticlinks > awstats.ws.preprod.tempr.me.urlexit.html
sudo awstats -config=ws.preprod.tempr.me -output=browserdetail -staticlinks > awstats.ws.preprod.tempr.me.browserdetail.html
sudo awstats -config=ws.preprod.tempr.me -output=osdetail -staticlinks > awstats.ws.preprod.tempr.me.osdetail.html
sudo awstats -config=ws.preprod.tempr.me -output=unknownbrowser -staticlinks > awstats.ws.preprod.tempr.me.unknownbrowser.html
sudo awstats -config=ws.preprod.tempr.me -output=unknownos -staticlinks > awstats.ws.preprod.tempr.me.unknownos.html
sudo awstats -config=ws.preprod.tempr.me -output=refererse -staticlinks > awstats.ws.preprod.tempr.me.refererse.html
sudo awstats -config=ws.preprod.tempr.me -output=refererpages -staticlinks > awstats.ws.preprod.tempr.me.refererpages.html
sudo awstats -config=ws.preprod.tempr.me -output=keyphrases -staticlinks > awstats.ws.preprod.tempr.me.keyphrases.html
sudo awstats -config=ws.preprod.tempr.me -output=keywords -staticlinks > awstats.ws.preprod.tempr.me.keywords.html
sudo awstats -config=ws.preprod.tempr.me -output=errors404 -staticlinks > awstats.ws.preprod.tempr.me.errors404.html