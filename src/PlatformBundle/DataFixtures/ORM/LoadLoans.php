<?php

namespace PlatformBundle\DataFixtures\ORM;

use PlatformBundle\Entity\Loan;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLoans implements FixtureInterface
{
    private $titles = array(
        'Lorem ipsum potenti mollis faucibus',
        'Etiam duis ut purus curabitur',
        'Etiam himenaeos morbi egestas massa',
        'Amet sed sociosqu faucibus platea',
        'Tristique malesuada facilisis habitasse risus',
        'Dictum tincidunt donec suspendisse tincidunt',
        'Torquent consectetur senectus etiam donec',
        'Pulvinar duis pellentesque placerat dapibus',
        'Ornare nibh interdum dolor duis',
        'Fames justo maecenas odio etiam',
        'Odio lectus vehicula placerat rutrum',
        'Aenean imperdiet curabitur potenti non',
        'Dapibus hac tempor nibh est',
        'Ut lectus neque donec ullamcorper',
        'Curae urna sapien duis faucibus',
        'Fusce eget accumsan donec fames',
        'Potenti cras porttitor interdum sociosqu',
        'Rutrum volutpat class nisi enim',
        'Eros nisi amet donec at',
        'Placerat proin cras venenatis at',
        'Far rank mammoth octopus widely',
        'Yikes temperately hence newt far',
        'Scratched porpoise beat ignobly therefore',
        'Facetious much jeepers next happy',
        'Gosh jay far before over',
        'A raccoon while ouch hence',
        'Boa after tryingly drew came',
        'Aimlessly before and yikes rightly',
        'Or educational one rat since',
        'The oriole changed scorpion beside',
        'Far a hot oh beyond',
        'That armadillo pithily gosh belched',
        'Indefatigably nimble hello noisily beneath',
        'Wherever misspelled owing and dachshund',
        'Harshly some macaw since this',
        'Wallaby menially bounced approvingly tortoise',
        'Narrowly ouch hey jeez waspish',
        'Hey much slapped unlike dove',
        'Hence prudently sullen far yet',
        'Yikes foresaw more past and',
        'Gosh that where wow far',
        'So much so far thanks',
        'Then toward the famous below',
        'Much far the evilly under',
        'Elephant oh saluted kangaroo stole',
        'Via goodness ouch crud let',
        'Save began much trout much',
        'Alas derisive that one explicit',
        'Bitter until goldfish following as',
        'Far far more so within',
        'One one this wow heinous',
        'Away because equivalently far fox',
        'Wherever returned followed wolf bought',
        'And therefore more stopped up',
        'Far preparatory near robin petulantly',
        'When much timorous shrank behind',
        'Alas more one some mean',
        'Unreceptively oh the less apologetic',
        'Irrespective robin fruitfully metric the',
        'Amidst echidna one foolhardy peered',
        'Far vulture hey forlorn baboon',
        'More this one when crud',
        'Mysterious hello annoying and within',
        'Far false a this burned',
        'Far far aside anonymously smelled',
        'Hey before hence and where',
        'Far beamed yet trite penguin',
        'Jeepers gecko jolly wildebeest continually',
        'Precarious and gurgled pert less',
        'And hello much gosh dear',
        'Far or the black and',
        'Much opposite far far apart',
        'Much messy a one excepting',
        'However the much gorilla and',
        'Falcon let but hello well',
        'Folded and ferret incapable more',
        'Excruciatingly meant according ouch while',
        'And jeez owl dryly lied',
        'A depending a far beneath',
        'Less some as so alas'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $max = count($this->titles);
        for ($i = 0; $i < $max; $i++) {
            $loan = new Loan();
            $amount = $this->randomFloat(10, 500);
            $loan->setAmount($amount);
            $loan->setAvailableForInvestments($amount);
            $loan->setDescription($this->randomSentence());
            $manager->persist($loan);
        }
        $manager->flush();
    }

    private function randomFloat($min, $max)
    {
        return ($min+lcg_value()*(abs($max-$min)));
    }

    public function randomSentence($len = 1)
    {
        shuffle($this->titles);
        return implode('. ', array_slice($this->titles, 0, $len));
    }

}