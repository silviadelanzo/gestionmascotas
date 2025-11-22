# Reglas del proyecto Mascotas y Mimos (Codex)

## 1. Archivos que NO se pueden tocar jamás
- public/config/db.php
- public/config/mail.php
- public/config/env.php
- public/.htaccess (solo modificar si yo lo apruebo)

## 2. Reglas para el index
- No modificar public/index.php directamente.
- Cualquier nueva versión se llama: index_vX.php (ejemplo: index_v2.php)

## 3. Ubicación obligatoria del código
- Front y páginas públicas: dentro de public/
- APIs: dentro de public/api/
- Includes: dentro de public/includes/
- Lógica interna/MVC: app/ (Controllers, Models, Views)

## 4. Estándares de programación
- PHP 8+
- PDO obligatorio usando db() en includes/bootstrap.php
- Sanitizado: usar htmlspecialchars siempre en valores mostrados
- Validaciones del lado del servidor obligatorias
- No escribir consultas SQL sin parámetros preparados

## 5. Estándares de diseño
- Usar Tailwind CSS (CDN) para nuevas pantallas
- Diseño mobile-first
- Imágenes optimizadas en public/assets/img/

## 6. Manejo del repositorio
- Cada modificación debe anunciarse antes de ejecutarse
- Debés pedirme autorización antes de tocar cualquier archivo
- Después de cada tarea: commit + push automático a main

## 7. Deploy
- El deploy se hace con .github/workflows/deploy.yml
- Solo subir public/ al servidor
- Nunca subir archivos sensibles
- Nunca subir SQL ni carpetas docs/ al servidor

## 8. Condición global
**No ejecutar ninguna acción sin autorización explícita del usuario.**

# Reglas para colaboraciones con Codex

Estas notas proporcionan pautas rápidas para trabajar con el asistente en este repositorio y mantener la coherencia del proyecto.

## Alcance
- Cubre cualquier interacción en la que se soliciten respuestas, cambios de código o documentación generada con la ayuda de Codex.
Complementa las instrucciones existentes en otros documentos; en caso de conflicto, prevalece lo indicado en las tareas o por los responsables del proyecto.

## Estilo de respuesta
- Responda en español de forma clara y concisa, con listas o subtítulos cuando facilite la lectura.
- Incluya pasos prácticos y cite las rutas de los archivos cuando se mencionen cambios específicos.
Evite tecnicismos innecesarios y aclare las suposiciones cuando algo no sea explícito.

## Cambios de código
- Antes de modificar los archivos, compruebe si existe documentación asociada en `docs/` o en carpetas cercanas.
- Mantener el formato y las convenciones existentes (sangrías, nombres de variables y comentarios en español).
- No elimine el contexto histórico sin confirmación; es preferible añadir notas explicativas.

## Commits y PRs
- Mensajes breves y descriptivos, en español, que mencionen el objetivo principal.
- Actualizar las pruebas o ejemplos cuando cambie la funcionalidad.
- En la solicitud de extracción, incluya un breve resumen, una lista de cambios y la verificación de las pruebas realizadas.

## Preguntas y respuestas rápidas
- Compruebe que no haya archivos temporales ni credenciales en la diferencia.
- Ejecutar las pruebas pertinentes cuando se modifiquen los componentes funcionales.
- Confirma que la nueva documentación tiene títulos y listas coherentes.

## Tono y comunicación
- Registrar las decisiones y los asuntos pendientes relevantes en las notas diarias ( `docs/NOTAS_CHAT/` ).
- En caso de ambigüedad, documente las suposiciones realizadas en el mismo commit o en la nota del día.
