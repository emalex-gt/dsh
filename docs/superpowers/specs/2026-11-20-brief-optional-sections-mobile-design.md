# Brief: Secciones opcionales y movil

## Intencion

Mantener la informacion de empresa y la configuracion de servicios como los dos unicos requisitos del brief. Recuperar las secciones historicas para clientes que quieran aportar mas contexto, sin bloquear el calculo ni la revision del presupuesto.

## Objetivos

- Ordenar el recorrido como `Empresa`, `Servicios` y despues las fases opcionales.
- Permitir enviar el brief a la estimacion desde Servicios, sin recorrer las fases opcionales.
- Mantener la navegacion secuencial para quien quiera completar informacion adicional.
- Eliminar requisitos HTML y validaciones visuales de las fases opcionales.
- Aumentar el espacio util en movil reduciendo los margenes, el espacio superior y el padding interno del brief.

## No objetivos

- No cambiar el catalogo, los calculos tecnicos, el presupuesto ni la confirmacion del brief.
- No eliminar campos existentes ni datos previamente enviados.
- No cambiar los requisitos de Empresa, Servicios ni de sus items obligatorios.

## Enfoques considerados

1. Restaurar todas las fases y ofrecer un salto global a la estimacion. Elegido porque conserva el brief completo sin obligar a navegar por cada fase.
2. Mostrar todas las fases complementarias en una pagina larga. Se descarta porque deteriora la lectura y el uso movil.
3. Mantener un boton de omitir por cada fase. Se descarta porque sigue obligando al cliente a atravesar cada pantalla.

## Diseno elegido

El registro de pasos activara todas las fases en este orden: `Empresa`, `Servicios`, `Fiscal`, `Objetivos`, `Publico`, `Inversion`, `Marca`, `Competencia`, `Automatizacion`, `Timeline`, `Materiales` y `Expectativas`.

`Empresa` y `Servicios` conservaran sus requisitos actuales. Todas las fases posteriores se marcaran como opcionales en la interfaz, se renderizaran con su condicion Alpine real y perderan atributos `required` y grupos de checkbox obligatorios.

En Servicios aparecera un CTA principal `Ver estimacion` que valida solo Empresa y Servicios y envia el formulario. El pie del asistente mostrara el mismo CTA en las fases opcionales, junto a `Continuar`, para que el cliente pueda dejar de completar informacion adicional en cualquier momento. El envio conserva los valores ya introducidos.

En movil, el layout del brief reducira el padding de la pagina a 8px, el bloque de marca superior a un margen compacto, el panel a esquinas y padding menores, y los encabezados y cuerpo del formulario usaran padding lateral de 16px. En escritorio se preserva el diseno actual.

## Verificacion

- Prueba de envio con solo Empresa y Servicios.
- Prueba de envio tras completar parcialmente una fase opcional.
- Render de `/brief` confirmando los doce pasos y el CTA de estimacion.
- Pruebas existentes de calculo y seleccion de servicios.
