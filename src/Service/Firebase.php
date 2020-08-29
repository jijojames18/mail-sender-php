<?php
namespace Service;

use Google\Cloud\Firestore\FirestoreClient;

class Firebase
{
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
        $collectionReference = $this->firestore->collection('websites');
        $documentReference = $collectionReference->document($this->site);
        $snapshot = $documentReference->snapshot();
        return $snapshot;
    }
}