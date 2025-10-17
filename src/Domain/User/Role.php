<?php
namespace Src\Domain\User;

enum Role: string {
    case ADMIN = "ADMIN";
    case OPERARIO = "OPERARIO";
}
