<?php
// Configuration de la base de données
require_once 'dbconfig.php';

// Headers pour l'API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Récupérer tous les événements de la base de données
    $query = "SELECT * FROM evenements ORDER BY date_debut DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll();

    // Formater les événements pour le frontend
    $formattedEvents = [];
    $currentDate = date('Y-m-d');

    foreach ($events as $event) {
        // Déterminer si l'événement est à venir ou passé
        $eventDate = date('Y-m-d', strtotime($event['date_debut']));
        $type = ($eventDate >= $currentDate) ? 'upcoming' : 'past';

        // Créer une image par défaut si aucune n'est spécifiée
        $image = !empty($event['image']) ? $event['image'] : 'https://picsum.photos/seed/rjvc' . $event['id'] . '/400/300.jpg';

        // Formater la date et l'heure
        $startDate = new DateTime($event['date_debut']);
        $endDate = !empty($event['date_fin']) ? new DateTime($event['date_fin']) : null;

        $formattedEvent = [
            'id' => (int)$event['id'],
            'title' => htmlspecialchars($event['titre']),
            'date' => $startDate->format('Y-m-d'),
            'endDate' => $endDate ? $endDate->format('Y-m-d') : $startDate->format('Y-m-d'),
            'time' => $startDate->format('H:i') . ($endDate ? ' - ' . $endDate->format('H:i') : ''),
            'category' => ucfirst($event['categorie']),
            'location' => !empty($event['lieu']) ? htmlspecialchars($event['lieu']) : 'Lieu à déterminer',
            'description' => htmlspecialchars($event['description']),
            'image' => $image,
            'type' => $type
        ];

        $formattedEvents[] = $formattedEvent;
    }

    // Retourner les événements au format JSON
    echo json_encode([
        'success' => true,
        'events' => $formattedEvents,
        'total' => count($formattedEvents)
    ]);

} catch (PDOException $e) {
    // En cas d'erreur, retourner un message d'erreur
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()
    ]);
}
?>
