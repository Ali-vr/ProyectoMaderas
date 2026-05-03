<?php

function html(?string $text = null): string
{
  return htmlspecialchars($text ?? "", ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
