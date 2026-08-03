<?php
class FormOptionService
{
    public function __construct(
        protected EmployeeRepository $employeeRepository,
        protected DepartmentRepository $departmentRepository,
        protected PositionRepository $positionRepository,
        protected CostCenterRepository $costCenterRepository,
    ) {}

    public function employees()
    {
        return $this->employeeRepository->getForSelect();
    }

    public function departments()
    {
        return $this->departmentRepository->getForSelect();
    }

    public function positions()
    {
        return $this->positionRepository->getForSelect();
    }

    public function costCenters()
    {
        return $this->costCenterRepository->getForSelect();
    }
}