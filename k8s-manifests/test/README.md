Create configmap for nginx configuration

in /docker/conf folder

```
kubectl create configmap swisscollections-test-nginx --from-file nginx.conf
```