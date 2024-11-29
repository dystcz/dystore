<?php

namespace Dystcz\LunarApiNewsletter\Drivers;

use Brevo\Client\Api\ContactsApi as Brevo;
use Brevo\Client\Configuration as BrevoConfiguration;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\CreateUpdateContactModel;
use Brevo\Client\Model\GetExtendedContactDetails;
use Brevo\Client\Model\PostContactInfo;
use Brevo\Client\Model\RemoveContactFromList;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\Drivers\Driver;
use Spatie\Newsletter\Support\Lists;

class BrevoDriver implements Driver
{
    protected Lists $lists;

    protected Brevo $brevo;

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    /**
     * @param  array<string,string>  $arguments
     */
    public function __construct(array $arguments, Lists $lists)
    {
        $config = BrevoConfiguration::getDefaultConfiguration()
            ->setApiKey(
                'api-key',
                $arguments['api_key'] ?? '',
            );

        $this->brevo = new Brevo(
            client: new GuzzleClient(),
            config: $config,
        );

        $this->lists = $lists;
    }

    /**
     * Get API instance.
     */
    public function getApi(): Brevo
    {
        return $this->brevo;
    }

    /**
     * Subscribe a user to a list.
     *
     * @param  array<string,string>  $properties
     * @param  array<string,mixed>  $options
     */
    public function subscribe(string $email, array $properties = [], string $listName = '', array $options = []): CreateUpdateContactModel|bool
    {
        $list = $this->lists->findByName($listName);

        $listIds = [(int) $list->getId()];

        return $this->createContact($email, $properties, $listIds, $options);
    }

    /**
     * Create a contact in Brevo.
     *
     * @param  array<string,string>  $properties
     * @param  int[]  $listIds
     * @param  array<string,mixed>  $options
     */
    protected function createContact(
        string $email,
        array $properties = [],
        array $listIds = [],
        array $options = []
    ): CreateUpdateContactModel|false {
        $createContact = new CreateContact();
        $createContact->setEmail($email);

        if (! empty($listIds)) {
            $createContact->setListIds($listIds);
        }

        if (! empty($properties) && ! array_is_list($properties)) {
            $createContact->setAttributes($properties);
        }

        try {
            $result = $this->brevo->createContact($createContact);
        } catch (Exception $e) {
            Log::error('Exception when creating a contact: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return $result;
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): CreateUpdateContactModel|bool {
        $list = $this->lists->findByName($listName);

        $listIds = [(int) $list->getId()];

        // Check if the contact exists
        $contact = $this->getMember($email, $listName);

        // If the contact does not exist, create it
        if (! $contact) {
            return $this->createContact($email, $properties, $listIds, $options);
        }

        // If the contact exists, update it
        return $this->updateContact($email, $properties, $listIds, $options);
    }

    /**
     * Update a contact in Brevo.
     *
     * @param  array<string,string>  $properties
     * @param  int[]  $listIds
     * @param  array<string,mixed>  $options
     */
    protected function updateContact(
        string $email,
        array $properties = [],
        array $listIds = [],
        array $options = [],
    ): bool {
        $updateContact = new \Brevo\Client\Model\UpdateContact();

        if (! empty($listIds)) {
            $updateContact->setListIds($listIds);
        }

        if (! empty($properties) && ! array_is_list($properties)) {
            $updateContact->setAttributes($properties);
        }

        try {
            $this->brevo->updateContact($email, $updateContact);
        } catch (Exception $e) {
            Log::error('Exception when updating a contact: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return true;
    }

    /**
     * Get member.
     */
    public function getMember(string $email, string $listName = '', bool $checkList = false): GetExtendedContactDetails|false
    {
        $list = $this->lists->findByName($listName);

        try {
            $contact = $this->brevo->getContactInfo($email);
        } catch (Exception $e) {
            Log::error('Exception when getting contact info: '.$e->getMessage().PHP_EOL, $e->getTrace());
        }

        if (! isset($contact)) {
            return false;
        }

        // If the contact is not in the list, return false
        if ($checkList && ! in_array((int) $list->getId(), $contact->getListIds())) {
            return false;
        }

        return $contact;
    }

    /**
     * Unsubscribe a contact from a list.
     */
    public function unsubscribe(string $email, string $listName = ''): bool|PostContactInfo
    {
        $list = $this->lists->findByName($listName);

        $contactEmails = new RemoveContactFromList();
        $contactEmails->setEmails([$email]);

        try {
            $result = $this->brevo->removeContactFromList((int) $list->getId(), $contactEmails);
        } catch (Exception $e) {
            Log::error('Exception when unsubscribing contact from a list: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return $result;
    }

    /**
     * Delete a contact.
     */
    public function delete(string $email, string $listName = ''): bool
    {
        try {
            $this->brevo->deleteContact($email);
        } catch (Exception $e) {
            Log::error('Exception deleting a contact: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return true;
    }

    /**
     * Check if a contact exists.
     */
    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember(email: $email, listName: $listName, checkList: false) ? true : false;
    }

    /**
     * Check if contact is subscribed to a list.
     */
    public function isSubscribed(string $email, string $listName = ''): bool
    {
        return $this->getMember(email: $email, listName: $listName, checkList: true) ? true : false;
    }
}
