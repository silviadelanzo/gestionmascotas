# Instrucciones para Diagnóstico Manual del Login

## 🎯 Objetivo
Identificar exactamente por qué el login funciona en local pero no en producción.

## 📋 Pasos a Seguir

### 1. Subir archivo de diagnóstico manualmente

**Archivo a subir**: `public/test_session.php`

**Cómo subirlo**:
1. Ir a tu panel de hosting (cPanel o similar)
2. Abrir el File Manager
3. Navegar a `public_html/gestionmascotas/public/`
4. Subir el archivo `test_session.php`

### 2. Ejecutar diagnóstico en LOCAL

1. Abrir en navegador: `http://localhost/gestionmascotas/public/test_session.php?debug=1`
2. **Copiar TODO el output** y guardarlo como `diagnostico_local.txt`
3. Refrescar la página (F5) varias veces
4. Verificar si `test_timestamp` y `test_random` **SE MANTIENEN** o **CAMBIAN**
   - ✅ Si se mantienen = Sesión funciona
   - ❌ Si cambian = Sesión NO funciona

### 3. Ejecutar diagnóstico en PRODUCCIÓN

1. Abrir en navegador: `https://mascotasymimos.com/gestionmascotas/public/test_session.php?debug=1`
2. **Copiar TODO el output** y guardarlo como `diagnostico_produccion.txt`
3. Refrescar la página (F5) varias veces
4. Verificar si `test_timestamp` y `test_random` **SE MANTIENEN** o **CAMBIAN**

### 4. Comparar resultados

Buscar diferencias entre `diagnostico_local.txt` y `diagnostico_produccion.txt`:

**Puntos críticos a comparar**:
- `session.save_path` - ¿Es escribible en producción?
- `session.cookie_domain` - ¿Es correcto?
- `session.cookie_path` - ¿Es correcto?
- `session.cookie_secure` - ¿Está en 1 en producción?
- `HTTPS` - ¿Está en YES en producción?
- Variables de sesión - ¿Persisten después de refresh?

## 🔍 Posibles Problemas y Soluciones

### Problema 1: `session.save_path` no es escribible
**Síntoma**: "Es escribible: ❌ NO"
**Solución**: Contactar soporte del hosting para que den permisos de escritura

### Problema 2: `session.cookie_domain` incorrecto
**Síntoma**: Domain está vacío o es diferente
**Solución**: Configurar explícitamente en `bootstrap.php`

### Problema 3: Variables de sesión cambian en cada refresh
**Síntoma**: `test_timestamp` y `test_random` son diferentes cada vez
**Solución**: La sesión no se está guardando - problema de permisos o configuración

### Problema 4: No hay cookies
**Síntoma**: "NO HAY COOKIES" en sección 5
**Solución**: El navegador está bloqueando cookies o el dominio es incorrecto

## 📝 Qué hacer con los resultados

Una vez que tengas ambos diagnósticos:

1. **Enviame los dos archivos** (`diagnostico_local.txt` y `diagnostico_produccion.txt`)
2. **Decime qué pasa** cuando refrescas en producción (¿cambian los números?)
3. Con esa información podré darte la solución exacta

## ⚡ Solución Rápida Alternativa

Si el diagnóstico muestra que las sesiones NO funcionan en producción, podemos implementar un sistema de autenticación con **tokens en cookies** que no depende de sesiones PHP:

```php
// En lugar de $_SESSION, usar cookies firmadas
setcookie('auth_token', $encrypted_user_data, [
  'expires' => time() + 3600,
  'path' => '/gestionmascotas/public',
  'domain' => 'mascotasymimos.com',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
```

Esto es más robusto pero requiere más código. Solo lo implementamos si las sesiones definitivamente no funcionan.
