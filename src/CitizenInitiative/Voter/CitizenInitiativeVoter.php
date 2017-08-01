<?php

namespace AppBundle\CitizenInitiative\Voter;

use AppBundle\CitizenInitiative\CitizenInitiativePermissions;
use AppBundle\Entity\Adherent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CitizenInitiativeVoter implements VoterInterface
{
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $adherent = $token->getUser();
        if (null !== $subject || !$adherent instanceof Adherent) {
            return self::ACCESS_ABSTAIN;
        }

        if (!in_array(CitizenInitiativePermissions::CREATE, $attributes, true)) {
            return self::ACCESS_ABSTAIN;
        }

        return $this->voteOnCreateCommitteeAttribute($adherent);
    }

    private function voteOnCreateCommitteeAttribute(Adherent $adherent)
    {
        if ($adherent->isReferent()) {
            return self::ACCESS_DENIED;
        }

        return $this->manager->isCommitteeHost($adherent) ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
    }
}
