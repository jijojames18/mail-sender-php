<?php
namespace Service;

use Google\Cloud\Firestore\FirestoreClient;
use Model\FirebaseDocument;

class Firebase
{
    const COLLECTION_NAME = 'websites';

    private $site = null;
    private $firestore = null;

    public function __construct($site)
    {
        $this->site = $site;
        $this->firestore = new FirestoreClient(
            array(
                'keyFile' => json_decode(FIREBASE_CREDENTIALS, true)
            )
        );
    }

    public function getDocument()
    {
        $collectionReference = $this->firestore->collection(self::COLLECTION_NAME);
        $documentReference = $collectionReference->document($this->site);
        $snapshot = $documentReference->snapshot();
        return new FirebaseDocument($snapshot);
    }
}
