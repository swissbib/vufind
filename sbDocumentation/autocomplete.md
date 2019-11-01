Some thoughts about autocomplete

VuFind Doc : https://vufind.org/wiki/configuration:autocomplete


Configuration in searches.ini

solr suggester : https://lucene.apache.org/solr/guide/7_3/suggester.html

Outermedia worked on the box in typescript



Flow of an autosuggest :

let's type algeb

author : 
http://localhost:8984/solr/green/select?fl=%2A%2Cscore&spellcheck=false&q.op=AND&hl=true&hl.simple.pre=%7B%7B%7B%7BSTART_HILITE%7D%7D%7D%7D&hl.simple.post=%7B%7B%7B%7BEND_HILITE%7D%7D%7D%7D&hl.fl=fulltext&hl.fl=title_short%2Cauthor%2Cauthor_additional&hl.fragsize=250&wt=json&json.nl=arrarr&rows=20&start=0&qf=title_short+author+author_additional&qt=dismax&mm=100%25&q=algeb


title
http://localhost:8984/solr/green/select?fl=%2A%2Cscore&spellcheck=false&q.op=AND&hl=true&hl.simple.pre=%7B%7B%7B%7BSTART_HILITE%7D%7D%7D%7D&hl.simple.post=%7B%7B%7B%7BEND_HILITE%7D%7D%7D%7D&hl.fl=fulltext&hl.fl=title_short%2Ctitle_alt%2Ctitle_additional_dsv11_txt_mv%2Ctitle_additional_gnd_txt_mv%2Ctitle_sub%2Ctitle_old%2Ctitle_new%2Cseries%2Cseries2&hl.fragsize=250&wt=json&json.nl=arrarr&rows=20&start=0&qf=title_short%5E1000+title_alt%5E200+title_additional_dsv11_txt_mv%5E200+title_additional_gnd_txt_mv%5E200+title_sub%5E200+title_old%5E200+title_new%5E200+series%5E100+series2+series%5E200&qt=edismax&bf=recip%28abs%28ms%28NOW%2FDAY%2Cfreshness%29%29%2C3.16e-10%2C100%2C100%29&mm=100%25&q=algeb

another author ??
http://localhost:8984/solr/green/select?fl=%2A%2Cscore&spellcheck=false&q.op=AND&hl=true&hl.simple.pre=%7B%7B%7B%7BSTART_HILITE%7D%7D%7D%7D&hl.simple.post=%7B%7B%7B%7BEND_HILITE%7D%7D%7D%7D&hl.fl=fulltext&hl.fl=author%2Cauthor_additional%2Cauthor_additional_dsv11_txt_mv%2Cauthor_additional_gnd_txt_mv&hl.fragsize=250&wt=json&json.nl=arrarr&rows=20&start=0&qf=author%5E100+author_additional%5E20+author_additional_dsv11_txt_mv%5E20+author_additional_gnd_txt_mv%5E20&qt=edismax&mm=100%25&q=algeb



Config

searches.ini
[Autocomplete]
default_handler = Solr:Autosuggest

-> tells to do the suggestions against the Autosuggest field from searchspecs.yml

Subject = "Solr:Subject:topic,genre,geographic,era"

-> tells to do the suggestions against the Subject field and display the content of the topic field (first priority) or the genre field or the geographic field, ...


[AutoSuggest]
new from outermedia

defines the searchbox design and links


searchspecs.yml

definir des search handlers pour le autosuggest



After a request, this is the flow : 

https://github.com/swissbib/vufind/blob/master/module/VuFind/src/VuFind/Autocomplete/Solr.php

getSuggestions

Mais il est overwriter par swissbib


Pour chaque champ, il fait une requete exacte pour avoir le nombre de résultats.
Puis il fait une recherche einstein*


first
getTotal
title einstein
puis 
title einstein*

idem
author einstein from getTotal
author einstein*

autosuggest einstein
autosuggest einstein*

cela devrait être subjectsuggest ???

bestmatch, c'est juste pour récupérer le meilleur champs si il y en a plusieurs

il y a aussi ça :
https://test.swissbib.ch/AJAX/JSON?q=algebra&method=getACSuggestions&searcher=Solr&type=Title

mais je sais pas ce que c'est. c'est ce qui lance le mécanisme je crois.

For getTotal : 
```json
{
"responseHeader": {
"zkConnected": true,
"status": 0,
"QTime": 7,
"params": {
"mm": "100%",
"qt": "edismax",
"hl": "true",
"json.nl": "arrarr",
"fl": "*,score",
"hl.fragsize": "250",
"start": "0",
"q.op": "AND",
"rows": "20",
"hl.simple.pre": "{{{{START_HILITE}}}}",
"q": "einstein",
"hl.simple.post": "{{{{END_HILITE}}}}",
"spellcheck": "false",
"qf": "author^100 author_additional^20 author_additional_dsv11_txt_mv^20 author_additional_gnd_txt_mv^20",
"hl.fl": [
"fulltext",
"author,author_additional,author_additional_dsv11_txt_mv,author_additional_gnd_txt_mv"
],
"wt": "json"
}
}
}
```

for einstein*
```json
{
"responseHeader": {
"zkConnected": true,
"status": 0,
"QTime": 10,
"params": {
"mm": "100%",
"qt": "edismax",
"hl": "true",
"json.nl": "arrarr",
"fl": "*,score",
"hl.fragsize": "250",
"start": "0",
"q.op": "AND",
"sort": "score desc",
"rows": "20",
"hl.simple.pre": "{{{{START_HILITE}}}}",
"q": "einstein*",
"hl.simple.post": "{{{{END_HILITE}}}}",
"spellcheck": "false",
"qf": "author^100 author_additional^20 author_additional_dsv11_txt_mv^20 author_additional_gnd_txt_mv^20",
"hl.fl": [
"fulltext",
"author,author_additional,author_additional_dsv11_txt_mv,author_additional_gnd_txt_mv"
],
"wt": "json"
}
}
}
```

Remarques : cela demande 20 lignes. Pourquoi ??? 3 suffisent. Non

C'est la même request à part l'étoile après einstein.

ça envoie une requete de recherche par auteur de Einstein* avec 20 résultats.

https://test.swissbib.ch/AJAX/JSON?q=einstein&method=getACSuggestions&searcher=Solr&type=Author

Avec ces 20 résultats, cela envoie à getSuggestionsFromSearch

Pour chacun des 20 résultats, cela recherche dans le champ author, cela cherche si la string cherchée (ou les mots) einstein (pickBestMatch) est dans la valeur du champ (par exemple albert einstein). Si oui, cela renvoie ce résultat.


En fait les suggestions sont exactement le résultat de l'appel à 
https://test.swissbib.ch/AJAX/JSON?q=bernar&method=getACSuggestions&searcher=Solr&type=Author

(du moins si on change pas autocomplete config Title ok mais pas Title:title_short)


en plus cela dépend du shard qui répond !!!!




Other ways : 

term based
author
http://localhost:8984/solr/suggestions/terms?terms.fl=navAuthor_full&terms.prefix=Einst&wt=xml

http://localhost:8984/solr/suggestions/terms?terms.fl=navSub_green&terms.prefix=Algeb&wt=xml




Suggester based

impossible to build title index

impossible de construire le title suggest index

curl 'http://localhost:8080/solr/suggestions/suggest-title?suggest=true&suggest.build=true&suggest.q=Die'
{
  "error":{
    "metadata":[
      "error-class","org.apache.solr.common.SolrException",
      "root-error-class","java.net.SocketTimeoutException"],
    "msg":"Error trying to proxy request for url: http://131.152.230.27:8080/solr/suggestions/suggest-title",
    "trace":"org.apache.solr.common.SolrException: Error trying to proxy request for url: http://131.152.230.27:8080/solr/suggestions/suggest-title\n\tat org.apache.solr.servlet.HttpSolrCall.remoteQuery(HttpSolrCall.java:647)\n\tat org.apache.solr.servlet.HttpSolrCall.call(HttpSolrCall.java:501)\n\tat org.apache.solr.servlet.SolrDispatchFilter.doFilter(SolrDispatchFilter.java:384)\n\tat org.apache.solr.servlet.SolrDispatchFilter.doFilter(SolrDispatchFilter.java:330)\n\tat org.eclipse.jetty.servlet.ServletHandler$CachedChain.doFilter(ServletHandler.java:1629)\n\tat org.eclipse.jetty.servlet.ServletHandler.doHandle(ServletHandler.java:533)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.handle(ScopedHandler.java:143)\n\tat org.eclipse.jetty.security.SecurityHandler.handle(SecurityHandler.java:548)\n\tat org.eclipse.jetty.server.handler.HandlerWrapper.handle(HandlerWrapper.java:132)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.nextHandle(ScopedHandler.java:190)\n\tat org.eclipse.jetty.server.session.SessionHandler.doHandle(SessionHandler.java:1595)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.nextHandle(ScopedHandler.java:188)\n\tat org.eclipse.jetty.server.handler.ContextHandler.doHandle(ContextHandler.java:1253)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.nextScope(ScopedHandler.java:168)\n\tat org.eclipse.jetty.servlet.ServletHandler.doScope(ServletHandler.java:473)\n\tat org.eclipse.jetty.server.session.SessionHandler.doScope(SessionHandler.java:1564)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.nextScope(ScopedHandler.java:166)\n\tat org.eclipse.jetty.server.handler.ContextHandler.doScope(ContextHandler.java:1155)\n\tat org.eclipse.jetty.server.handler.ScopedHandler.handle(ScopedHandler.java:141)\n\tat org.eclipse.jetty.server.handler.ContextHandlerCollection.handle(ContextHandlerCollection.java:219)\n\tat org.eclipse.jetty.server.handler.HandlerCollection.handle(HandlerCollection.java:126)\n\tat org.eclipse.jetty.server.handler.HandlerWrapper.handle(HandlerWrapper.java:132)\n\tat org.eclipse.jetty.rewrite.handler.RewriteHandler.handle(RewriteHandler.java:335)\n\tat org.eclipse.jetty.server.handler.HandlerWrapper.handle(HandlerWrapper.java:132)\n\tat org.eclipse.jetty.server.Server.handle(Server.java:530)\n\tat org.eclipse.jetty.server.HttpChannel.handle(HttpChannel.java:347)\n\tat org.eclipse.jetty.server.HttpConnection.onFillable(HttpConnection.java:256)\n\tat org.eclipse.jetty.io.AbstractConnection$ReadCallback.succeeded(AbstractConnection.java:279)\n\tat org.eclipse.jetty.io.FillInterest.fillable(FillInterest.java:102)\n\tat org.eclipse.jetty.io.ChannelEndPoint$2.run(ChannelEndPoint.java:124)\n\tat org.eclipse.jetty.util.thread.strategy.EatWhatYouKill.doProduce(EatWhatYouKill.java:247)\n\tat org.eclipse.jetty.util.thread.strategy.EatWhatYouKill.produce(EatWhatYouKill.java:140)\n\tat org.eclipse.jetty.util.thread.strategy.EatWhatYouKill.run(EatWhatYouKill.java:131)\n\tat org.eclipse.jetty.util.thread.ReservedThreadExecutor$ReservedThread.run(ReservedThreadExecutor.java:382)\n\tat org.eclipse.jetty.util.thread.QueuedThreadPool.runJob(QueuedThreadPool.java:708)\n\tat org.eclipse.jetty.util.thread.QueuedThreadPool$2.run(QueuedThreadPool.java:626)\n\tat java.lang.Thread.run(Thread.java:748)\nCaused by: java.net.SocketTimeoutException: Read timed out\n\tat java.net.SocketInputStream.socketRead0(Native Method)\n\tat java.net.SocketInputStream.socketRead(SocketInputStream.java:116)\n\tat java.net.SocketInputStream.read(SocketInputStream.java:171)\n\tat java.net.SocketInputStream.read(SocketInputStream.java:141)\n\tat org.apache.http.impl.io.SessionInputBufferImpl.streamRead(SessionInputBufferImpl.java:137)\n\tat org.apache.http.impl.io.SessionInputBufferImpl.fillBuffer(SessionInputBufferImpl.java:153)\n\tat org.apache.http.impl.io.SessionInputBufferImpl.readLine(SessionInputBufferImpl.java:282)\n\tat org.apache.http.impl.conn.DefaultHttpResponseParser.parseHead(DefaultHttpResponseParser.java:138)\n\tat org.apache.http.impl.conn.DefaultHttpResponseParser.parseHead(DefaultHttpResponseParser.java:56)\n\tat org.apache.http.impl.io.AbstractMessageParser.parse(AbstractMessageParser.java:259)\n\tat org.apache.http.impl.DefaultBHttpClientConnection.receiveResponseHeader(DefaultBHttpClientConnection.java:163)\n\tat org.apache.http.impl.conn.CPoolProxy.receiveResponseHeader(CPoolProxy.java:165)\n\tat org.apache.http.protocol.HttpRequestExecutor.doReceiveResponse(HttpRequestExecutor.java:273)\n\tat org.apache.http.protocol.HttpRequestExecutor.execute(HttpRequestExecutor.java:125)\n\tat org.apache.solr.util.stats.InstrumentedHttpRequestExecutor.execute(InstrumentedHttpRequestExecutor.java:118)\n\tat org.apache.http.impl.execchain.MainClientExec.execute(MainClientExec.java:272)\n\tat org.apache.http.impl.execchain.ProtocolExec.execute(ProtocolExec.java:185)\n\tat org.apache.http.impl.execchain.RetryExec.execute(RetryExec.java:89)\n\tat org.apache.http.impl.execchain.RedirectExec.execute(RedirectExec.java:111)\n\tat org.apache.http.impl.client.InternalHttpClient.doExecute(InternalHttpClient.java:185)\n\tat org.apache.http.impl.client.CloseableHttpClient.execute(CloseableHttpClient.java:83)\n\tat org.apache.http.impl.client.CloseableHttpClient.execute(CloseableHttpClient.java:56)\n\tat org.apache.solr.servlet.HttpSolrCall.remoteQuery(HttpSolrCall.java:619)\n\t... 36 more\n",
    "code":500}}
    
    

multiple : 

http://localhost:8984/solr/suggestions/suggest-title-author?suggest=true&suggest.q=einst&suggest.dictionary=suggestTitle&suggest.dictionary=suggestAuthor&suggest.count=2


sans build : 
http://localhost:8984/solr/suggestions/suggest-title?suggest=true&suggest.q=test

avec build : 
http://localhost:8984/solr/suggestions/suggest-title?suggest=true&suggest.build=true&suggest.q=Die


auteur : 

sans build : 
http://localhost:8984/solr/suggestions/suggest-author?suggest=true&suggest.q=einst

avec build : 
http://localhost:8984/solr/suggestions/suggest-author?suggest=true&suggest.build=true&suggest.q=einst


subject : 

sans build : 
http://localhost:8984/solr/suggestions/suggest-topic?suggest=true&suggest.q=algeb

avec build : 
http://localhost:8984/solr/suggestions/suggest-topic?suggest=true&suggest.build=true&suggest.q=algeb


il faudrait rajouter des weights











