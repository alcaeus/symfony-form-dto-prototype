<?php

namespace App\DTO;

use App\Form\DtoFormFactory;
use App\Metadata\Dto;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;
use function get_debug_type;
use function sprintf;

class HttpFormRequestHandler implements HandlerInterface
{
    /** @var DtoFormFactory */
    private $dtoFormFactory;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ViolationMapperInterface */
    private $violationMapper;

    public function __construct(DtoFormFactory $formFactory, ValidatorInterface $validator)
    {
        $this->dtoFormFactory = $formFactory;
        $this->validator = $validator;
        $this->violationMapper = new ViolationMapper();
    }

    /** @param Request $data */
    public function handle(Dto $metadata, WrappingDto $dto, $data): bool
    {
        if (!$data instanceof Request) {
            throw new class (sprintf('Cannot handle data of type "%s".', get_debug_type($data))) extends \RuntimeException {};
        }

        $form = $this->dtoFormFactory->create($metadata, $dto)->getForm();

        $form->handleRequest($data);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        // Validate DTO. Shamelessly copied from ValidationListener
        $violations = $this->validator->validate($form);
        foreach ($violations as $violation) {
            // Allow the "invalid" constraint to be put onto
            // non-synchronized forms
            $allowNonSynchronized = $violation->getConstraint() instanceof Form && Form::NOT_SYNCHRONIZED_ERROR === $violation->getCode();

            $this->violationMapper->mapViolation($violation, $form, $allowNonSynchronized);
        }

        return count($violations) === 0;
    }
}
