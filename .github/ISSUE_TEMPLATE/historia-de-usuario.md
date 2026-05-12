---
name: Historia de Usuario
about: Crear un ticket de historia de usuario
title: 'US-[ID]: [Título de la Historia de Usuario]'
labels: ''
assignees: ''
type: Feature

---

# US-01: Iniciar sesión con email y contraseña

**Narrativa de Usuario**
> **Como** cliente de Hierro y Madera
> **Quiero** iniciar sesión con mi email y contraseña
> **Para** acceder a mi cuenta y gestionar mis pedidos y compras

**Descripción / Contexto**
El cliente necesita autenticarse en la plataforma para poder realizar compras, ver el historial de pedidos y gestionar sus datos personales. El formulario de login debe validar los campos antes de enviar la solicitud al servidor.

## Criterios de Aceptación

1. **Escenario: Inicio de sesión exitoso**
    * **Dado que** el cliente tiene una cuenta registrada en la tienda
    * **Cuando** ingresa su email y contraseña correctos y hace clic en "Iniciar sesión"
    * **Entonces** el sistema lo redirige a la página principal con su sesión activa
    * **Y** se muestra su nombre o email en el encabezado de la página

2. **Escenario: Credenciales incorrectas**
    * **Dado que** el cliente está en el formulario de inicio de sesión
    * **Cuando** ingresa un email o contraseña incorrectos
    * **Entonces** el sistema debe mostrar el mensaje de error: "Email o contraseña incorrectos"

3. **Escenario: Validación de campos obligatorios**
    * **Dado que** el cliente está en el formulario de inicio de sesión
    * **Cuando** intenta enviar el formulario sin completar el campo email o contraseña
    * **Entonces** el sistema debe mostrar el mensaje de error: "Este campo es obligatorio"

## Comentarios Adicionales
* Depende de que el sistema de registro (US-02) esté implementado.
* El email debe validarse con formato correcto (ejemplo@mail.com).