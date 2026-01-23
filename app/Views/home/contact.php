<div class="page-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
</div>

<section class="content">
    <form action="/contact" method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <textarea name="message" placeholder="Mensaje" rows="5" required></textarea>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</section>