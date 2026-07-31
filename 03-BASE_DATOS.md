# Base de Datos

## Tabla mpios

id

nombre

logotipo

activo

timestamps

---

## Tabla departamentos

id

mpio_id

nombre

clave

director

email_contacto

activo

timestamps

---

## Tabla users

id

mpio_id

departamento_id

name

email

password

status

activo

remember_token

timestamps

---

## Tabla oficios

id

numero_unico

remitente_id

destinatario_id

creador_id

asunto

cuerpo_html

requiere_respuesta

plazo_dias

fecha_limite

estado

prioridad

pdf_path

tiene_adjuntos

deleted_at

timestamps

---

Estados

BORRADOR

ENVIADO

RECIBIDO

LEIDO

EN_RESPUESTA

RESPONDIDO

VENCIDO

CERRADO

---

Prioridades

BAJA

NORMAL

ALTA

URGENTE

---

## Tabla respuestas

id

oficio_id

autor_id

cuerpo_html

pdf_path

fecha_respuesta

timestamps

---

## Tabla media

Spatie Media Library

Colecciones:

id

model_type

model_id

uuid

collection_name

name

file_name

mime_type

disk

conversions_disk

size

manipulations

custom_properties

generated_conversions

responsive_images

order_column

created_at

updated_at

---

Relaciones

Municipio

hasMany Departamentos

Departamento

belongsTo Municipio

hasMany Usuarios

hasMany Oficios enviados

hasMany Oficios recibidos

Usuario

belongsTo Departamento

Oficio

belongsTo Remitente

belongsTo Destinatario

belongsTo Usuario

hasMany Respuestas

hasMany Media
